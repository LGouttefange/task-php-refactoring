<?php

namespace App\Infrastructure\Http\Bin;

use App\Exception\BinNotFoundException;
use App\Infrastructure\Http\Bin\Dto\BinResult;

interface BinListFetcherInterface
{
    /**
     * @throws BinNotFoundException
     */
    public function getBin(string $binCode): BinResult;
}
