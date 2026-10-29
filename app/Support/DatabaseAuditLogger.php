<?php

namespace App\Support;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseAuditLogger
{
    private static bool $writingAuditLog = false;
    private static ?bool $auditTableExists = null;
    private static array $pendingLogs = [];

    public static function beginRequest(): void
    {
        self::$pendingLogs = [];
    }

    public static function capture(QueryExecuted $query): void
    {
        if (self::$writingAuditLog) {
            return;
        }

        $change = self::describeWrite($query->sql, $query->bindings, $query->connection);
        if ($change === null || !Auth::check()) {
            return;
        }

        $request = request();
        self::$pendingLogs[] = [
            'user_id' => Auth::id(),
            'action' => $change['action'],
            'table_name' => $change['table'],
            'record_id' => $change['record_id'],
            'changes' => $change['changes'] === [] ? null : json_encode($change['changes'], JSON_THROW_ON_ERROR),
            'route' => $request->route()?->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ];
    }

    public static function flushRequest(): void
    {
        if (self::$pendingLogs === []) {
            return;
        }

        self::$auditTableExists ??= Schema::hasTable('database_audit_logs');
        if (!self::$auditTableExists) {
            self::$pendingLogs = [];
            return;
        }

        self::$writingAuditLog = true;
        try {
            foreach (self::$pendingLogs as $log) {
                DB::table('database_audit_logs')->insert($log);
            }
            self::$pendingLogs = [];
        } finally {
            self::$writingAuditLog = false;
        }
    }

    private static function describeWrite(string $sql, array $bindings, Connection $connection): ?array
    {
        if (!preg_match('/^\s*(insert|replace|update|delete)\b/i', $sql, $operation)) {
            return null;
        }

        $action = strtoupper($operation[1]);
        $tablePattern = match (strtolower($operation[1])) {
            'insert', 'replace' => '/\binto\s+[`"\[]?([\w$.]+)[`"\]]?/i',
            'update' => '/^\s*update\s+[`"\[]?([\w$.]+)[`"\]]?/i',
            'delete' => '/\bfrom\s+[`"\[]?([\w$.]+)[`"\]]?/i',
        };

        if (!preg_match($tablePattern, $sql, $tableMatch)) {
            return null;
        }

        $table = self::unquoteIdentifier($tableMatch[1]);
        $columns = [];

        if (in_array($action, ['INSERT', 'REPLACE'], true)
            && preg_match('/\binto\s+[`"\[]?[\w$.]+[`"\]]?\s*\((.*?)\)\s*values\s*\(/is', $sql, $insertMatch)
        ) {
            $columns = self::parseColumnList($insertMatch[1]);
        } elseif ($action === 'UPDATE'
            && preg_match('/\bset\s+(.*?)(?:\s+where\s+|$)/is', $sql, $updateMatch)
        ) {
            foreach (preg_split('/\s*,\s*/', $updateMatch[1], -1, PREG_SPLIT_OFFSET_CAPTURE) as [$assignment, $offset]) {
                if (preg_match('/[`"\[]?([\w$]+)[`"\]]?\s*=\s*\?/i', $assignment, $columnMatch)) {
                    $column = self::unquoteIdentifier($columnMatch[1]);
                    $bindingIndex = substr_count(substr($sql, 0, strpos($sql, $updateMatch[1]) + $offset), '?');
                    if (array_key_exists($bindingIndex, $bindings)) {
                        $columns[$bindingIndex] = $column;
                    }
                }
            }
        }

        $changes = [];
        foreach ($columns as $index => $column) {
            $changes[$column] = [
                'new' => self::isSensitiveColumn($column)
                    ? '[masqué]'
                    : self::normalizeValue($bindings[$index]),
            ];
        }

        $recordId = null;
        if (preg_match('/\bwhere\b/i', $sql, $whereMatch, PREG_OFFSET_CAPTURE)) {
            $whereOffset = $whereMatch[0][1] + strlen($whereMatch[0][0]);
            $whereSql = substr($sql, $whereOffset);
        } else {
            $whereOffset = strlen($sql);
            $whereSql = '';
        }

        if (preg_match('/(?:[`"\[]?)(?:[\w$.]+\.)?id(?:[`"\]]?)\s*=\s*\?/i', $whereSql, $idMatch, PREG_OFFSET_CAPTURE)) {
            $placeholderOffset = $whereOffset + $idMatch[0][1];
            $bindingIndex = substr_count(substr($sql, 0, $placeholderOffset), '?');
            $recordId = $bindings[$bindingIndex] ?? null;
        } elseif (in_array($action, ['INSERT', 'REPLACE'], true)) {
            $recordId = $connection->getPdo()->lastInsertId() ?: null;
        }

        return [
            'action' => $action,
            'table' => $table,
            'record_id' => $recordId === null ? null : (string) $recordId,
            'changes' => $changes,
        ];
    }

    private static function parseColumnList(string $columns): array
    {
        return array_map(
            static fn (string $column): string => self::unquoteIdentifier(trim($column)),
            preg_split('/\s*,\s*/', $columns) ?: []
        );
    }

    private static function unquoteIdentifier(string $identifier): string
    {
        return trim($identifier, " \t\n\r\0\x0B`\"[]");
    }

    private static function isSensitiveColumn(string $column): bool
    {
        return (bool) preg_match(
            '/password|token|secret|authorization|cookie|remember|email|phone|telephone|tel|whats.?app|address|adresse|carte|card|photo|image|document|file|otp|pin/i',
            $column
        );
    }

    private static function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return '[valeur non sérialisable]';
    }
}
