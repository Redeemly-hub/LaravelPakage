<?php

namespace LuckyCode\IntegrationHelper\Models;

class PullCodeRequest
{
    public function __construct(array $data = [])
    {
        $this->orderRef = $data['orderRef'] ?? null;
        $this->customerRef = $data['customerRef'] ?? null;
        $this->customerName = $data['customerName'] ?? null;
        $this->mobileCountryCode = $data['mobileCountryCode'] ?? null;
        $this->customerNumber = $data['customerNumber'] ?? null;
        $this->customerEmail = $data['customerEmail'] ?? null;
        $this->campaignRef = $data['campaignRef'] ?? null;
        $this->isReveal = (bool) ($data['isReveal'] ?? false);
        $this->orderDetails = $data['orderDetails'] ?? null;
        $this->requiredRules = $data['requiredRules'] ?? [];
        $this->optionalRules = $data['optionalRules'] ?? [];
    }

    public ?string $orderRef = null;
    public ?string $customerRef = null;
    public ?string $customerName = null;
    public ?string $mobileCountryCode = null;
    public ?string $customerNumber = null;
    public ?string $customerEmail = null;
    public ?string $campaignRef = null;
    public bool $isReveal = false;
    public mixed $orderDetails = null;
    public array $requiredRules = [];
    public array $optionalRules = [];
}

