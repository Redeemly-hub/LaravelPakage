<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\AppLanguage;

class HowToUseModel
{
    public function __construct(
        public AppLanguage $language,
        public bool $isUrl,
        public string $data
    ) {
    }
}

