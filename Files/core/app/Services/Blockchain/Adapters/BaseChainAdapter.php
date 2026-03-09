<?php

namespace App\Services\Blockchain\Adapters;

use RuntimeException;

abstract class BaseChainAdapter
{
    protected function guardEnabled(string $chain): void
    {
        if (!config("chains.$chain.enabled")) {
            throw new RuntimeException(strtoupper($chain) . ' integration is disabled');
        }
    }

    protected function mockAddress(string $prefix): string
    {
        return $prefix . substr(hash('sha256', uniqid($prefix, true)), 0, 30);
    }
}
