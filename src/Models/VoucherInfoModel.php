<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\VoucherType;

class VoucherInfoModel
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $enName = null,
        public ?string $arName = null,
        public ?string $reference = null,
        public ?string $description = null,
        public ?string $enDescription = null,
        public ?string $arDescription = null,
        public ?string $note = null,
        public ?string $image = null,
        public ?VoucherType $type = null,
        public array $howToUse = [],
    ) {
    }
}

