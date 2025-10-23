<?php

namespace LuckyCode\IntegrationHelper\Models;

class CustomerLogModel
{
    public function __construct(
        public ?int $orderId = null,
        public ?string $orderReference = null,
        public ?string $customerReference = null,
        public ?string $serialCode = null,
        public ?string $serialNumber = null,
        public ?string $revealCode = null,
        public ?bool $isValid = null,
        public ?bool $isPull = null,
        public ?bool $isReveal = null,
        public ?bool $isRedeem = null,
        public ?bool $isExpired = null,
        public ?string $expirationDate = null,
        public ?string $operationDate = null,
        public ?VoucherInfoModel $voucherInfo = null,
    ) {
    }
}

