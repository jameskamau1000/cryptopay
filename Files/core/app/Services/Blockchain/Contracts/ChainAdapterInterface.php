<?php

namespace App\Services\Blockchain\Contracts;

interface ChainAdapterInterface
{
    public function chain(): string;

    /**
     * @return array{address:string,memo:?string}
     */
    public function generateAddress(array $context = []): array;

    /**
     * @return array<int, array{
     *  tx_hash:string,
     *  amount:float,
     *  confirmations:int,
     *  block_number:int|null,
     *  to_address:string,
     *  payload:array
     * }>
     */
    public function findIncomingTransfers(string $address, string $asset, ?int $sinceBlock = null): array;

    /**
     * @return array{tx_hash:string,fee:float,payload:array}
     */
    public function sendAsset(array $transfer): array;

    /**
     * @return array{confirmations:int,block_number:int|null,status:string,payload:array}|null
     */
    public function getTransferStatus(string $txHash, array $context = []): ?array;
}
