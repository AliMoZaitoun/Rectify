<?php

namespace App\Services;

interface TransactionService
{
    public function execute(callable $callback): mixed;
}
