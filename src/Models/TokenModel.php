<?php

namespace LuckyCode\IntegrationHelper\Models;

class TokenModel
{
    public function __construct(
        public string $accessToken,
        public ?int $expiresIn = null
    ) {
    }
}

