<?php

namespace App\Services\Blockchain;

use App\Services\Blockchain\Adapters\BscAdapter;
use App\Services\Blockchain\Adapters\EthereumAdapter;
use App\Services\Blockchain\Adapters\TonAdapter;
use App\Services\Blockchain\Adapters\TronAdapter;
use App\Services\Blockchain\Contracts\ChainAdapterInterface;
use InvalidArgumentException;

class ChainManager
{
    public function for(string $chain): ChainAdapterInterface
    {
        return match (strtolower($chain)) {
            'tron' => app(TronAdapter::class),
            'eth', 'ethereum' => app(EthereumAdapter::class),
            'bsc', 'bep20' => app(BscAdapter::class),
            'ton' => app(TonAdapter::class),
            default => throw new InvalidArgumentException('Unsupported chain [' . $chain . ']'),
        };
    }
}
