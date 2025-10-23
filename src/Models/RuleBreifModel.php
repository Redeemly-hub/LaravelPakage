<?php

namespace LuckyCode\IntegrationHelper\Models;

class RuleBreifModel
{
    public function __construct(
        public ?string $key = null,
        public ?string $value = null
    ) {
    }
}

