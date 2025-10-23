<?php

namespace LuckyCode\IntegrationHelper\Models;

class RevealCodeRequest
{
    public function __construct(array $data = [])
    {
        $this->orderRef = $data['orderRef'] ?? null;
        $this->revealCode = $data['revealCode'] ?? null;
    }

    public ?string $orderRef = null;
    public ?string $revealCode = null;
}

