<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\CampaignStatus;

class CheckSerialCodeModel
{
    public function __construct(
        public ?string $serialCode = null,
        public ?string $serialNumber = null,
        public ?string $revealCode = null,
        public ?bool $isExpired = null,
        public ?bool $isRedeem = null,
        public ?string $campaignRef = null,
        public ?bool $canRedeem = null,
        public ?CampaignStatus $campaignStatus = null,
        public ?VoucherModel $voucher = null,
        public ?OrderModel $order = null,
        public ?VendorModel $vendor = null,
    ) {
    }
}

