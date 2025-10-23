<?php

namespace LuckyCode\IntegrationHelper\Models;

class VendorModel
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $businessReference = null,
        public ?int $merchantId = null,
        public ?string $merchantName = null,
        public ?bool $isActive = null,
    ) {
    }
}

