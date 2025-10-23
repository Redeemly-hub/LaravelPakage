<?php

namespace LuckyCode\IntegrationHelper\Models;

class RevealCodeResponse
{
    public function __construct(
        public ?string $serialCode = null,
        public ?string $serialNumber = null,
        public ?string $redeemValidateDate = null,
        public ?VoucherModel $voucher = null,
        public ?OrderModel $order = null,
        public ?VendorModel $vendor = null,
    ) {
    }
}

