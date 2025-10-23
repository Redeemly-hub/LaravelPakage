<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\RedeemResult;

class RedeemCodeResponse
{
    public function __construct(
        public ?string $vendorName = null,
        public ?string $serialCode = null,
        public ?VoucherModel $voucher = null,
        public ?RedeemResult $redeemResult = null,
        public ?OrderModel $order = null,
        public ?VendorModel $vendor = null,
    ) {
    }
}

