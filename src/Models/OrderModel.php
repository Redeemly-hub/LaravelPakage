<?php

namespace LuckyCode\IntegrationHelper\Models;

class OrderModel
{
    public function __construct(
        public ?string $orderRef = null,
        public ?string $customerRef = null,
        public ?string $customerName = null,
        public ?string $customerNumber = null,
        public ?string $customerEmail = null,
        public ?string $mobileCountryCode = null,
    ) {
    }
}

