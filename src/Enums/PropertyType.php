<?php

namespace LuckyCode\IntegrationHelper\Enums;

enum PropertyType: int
{
    case STRING = 1;
    case BOOLEAN = 2;
    case NUMBER = 3;
    case RANGE = 4;
    case GREATER_THAN = 5;
    case LESS_THAN = 6;
}

