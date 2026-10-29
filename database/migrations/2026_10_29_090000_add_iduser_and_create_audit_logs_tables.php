<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'transferts',
        'expeditions',
        'conteneurs',
        'approcaisses',
        'depenses',
        'paymentcolis',
        'tarifexpeditions',
        'tauxtransferts',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasColumn($tableName, 'iduser')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('iduser')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }

        Schema::create('database_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('action', 10);
            $table->string('table_name', 191);
            $table->string('record_id', 191)->nullable();
            $table->json('changes')->nullable();
            $table->string('route', 191)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('database_audit_logs');

        foreach ($this->tables as $tableName) {
            if ($tableName === 'depenses' || !Schema::hasColumn($tableName, 'iduser')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['iduser']);
                $table->dropColumn('iduser');
            });
        }
    }
};
