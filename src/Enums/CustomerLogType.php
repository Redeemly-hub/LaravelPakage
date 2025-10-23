<?php

namespace LuckyCode\IntegrationHelper\Enums;

enum CustomerLogType: int
{
    case New = 1;
    case Revealed = 2;
    case Redeemed = 3;
    case Expired = 4;
}

