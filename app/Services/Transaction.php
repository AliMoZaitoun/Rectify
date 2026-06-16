<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class Transaction implements TransactionService
{
    public function execute(callable $callback): mixed
    {
        return DB::transaction(fn() => $callback());
    }
}
