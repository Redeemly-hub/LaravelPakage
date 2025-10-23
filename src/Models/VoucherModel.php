<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\VoucherType;

class VoucherModel
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $enName = null,
        public ?string $arName = null,
        public ?string $description = null,
        public ?string $enDescription = null,
        public ?string $arDescription = null,
        public ?string $photo = null,
        public ?string $voucherRef = null,
        public ?VoucherType $type = null,
        public ?string $note = null,
        public ?float $amount = null,
        public array $howToUse = [],
        public ?array $requiredRules = null,
        public ?array $optionalRules = null,
        public ?array $requestRequiredRules = null,
        public ?array $requestOptionalRules = null,
    ) {
    }
}

