<?php

namespace LuckyCode\IntegrationHelper\Support;

class ErrorDto
{
    public function __construct(
        public ?string $code = null,
        public ?string $message = null
    ) {
    }
}

