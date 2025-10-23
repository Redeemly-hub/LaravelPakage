<?php

namespace LuckyCode\IntegrationHelper\Models;

class CredentialModel
{
    public function __construct(
        public string $apiKey,
        public string $clientId
    ) {
    }
}

