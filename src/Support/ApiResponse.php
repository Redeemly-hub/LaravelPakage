<?php

namespace LuckyCode\IntegrationHelper\Support;

class ApiResponse
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?ErrorDto $error = null,
        public string $sourceProvider = 'Likecard-Luckycode',
        public string $sourceProviderRef = '01092024'
    ) {
    }
}

