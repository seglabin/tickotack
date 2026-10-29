<?php

namespace App\Http\Middleware;

use App\Support\DatabaseAuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditDatabaseWrites
{
    public function handle(Request $request, Closure $next): Response
    {
        DatabaseAuditLogger::beginRequest();

        try {
            return $next($request);
        } finally {
            DatabaseAuditLogger::flushRequest();
        }
    }
}
