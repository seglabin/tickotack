<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function userCanModifyRecord(object $record): bool
    {
        return (int) (Auth::user()->idrole ?? 0) === 1
            || (int) ($record->iduser ?? 0) === (int) Auth::id();
    }

    protected function authorizeRecordOwner(object $record): void
    {
        abort_unless($this->userCanModifyRecord($record), 403);
    }
}
