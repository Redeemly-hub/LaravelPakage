<?php

namespace LuckyCode\IntegrationHelper\Models;

class PullCodeResponse
{
    public function __construct(
        public ?string $serialCode = null,
        public ?string $serialNumber = null,
        public ?string $revealCode = null,
        public ?int $revealValidate = null,
        public ?string $revealExpireDate = null,
        public ?VoucherModel $voucher = null,
        public ?OrderModel $order = null,
    ) {
    }
}

