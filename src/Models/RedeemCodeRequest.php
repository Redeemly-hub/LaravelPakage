<?php

namespace LuckyCode\IntegrationHelper\Models;

class RedeemCodeRequest
{
    public function __construct(array $data = [])
    {
        $this->serialCode = $data['serialCode'] ?? null;
        $this->cartValue = isset($data['cartValue']) ? (float) $data['cartValue'] : null;
    }

    public ?string $serialCode = null;
    public ?float $cartValue = null;
}

