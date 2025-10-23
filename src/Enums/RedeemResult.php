<?php

namespace LuckyCode\IntegrationHelper\Enums;

enum RedeemResult: string
{
    case Success = 'Success';
    case USED = 'USED';
    case EXPIRED = 'EXPIRED';
    case NEED_REVEAL = 'NEED_REVEAL';
    case INVALID = 'INVALID';
    case CANNOT_REDEEM = 'CANNOT_REDEEM';
}

