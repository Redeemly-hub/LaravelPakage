<?php

namespace LuckyCode\IntegrationHelper\Models;

use LuckyCode\IntegrationHelper\Enums\PropertyType;

class RuleModel extends RuleBreifModel
{
    public function __construct(
        ?string $key = null,
        ?string $value = null,
        public ?PropertyType $type = null
    ) {
        parent::__construct($key, $value);
    }
}

