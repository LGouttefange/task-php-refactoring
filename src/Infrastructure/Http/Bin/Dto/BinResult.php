<?php

namespace App\Infrastructure\Http\Bin\Dto;

readonly class BinResult
{
    public function __construct(
        public string $alpha2, 
    )
    {
    }

}
