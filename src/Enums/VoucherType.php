<?php

namespace LuckyCode\IntegrationHelper\Enums;

enum VoucherType: string
{
    case PRODUCT = 'PRODUCT';
    case DISCOUNT_PERCENT = 'DISCOUNT_PERCENT';
    case DISCOUNT_FIXED_AMOUNT = 'DISCOUNT_FIXED_AMOUNT';
    case POINT = 'POINT';
    case BALANCE = 'BALANCE';
    case FREE_SHIPPING_PERCENT = 'FREE_SHIPPING_PERCENT';
    case FREE_SHIPPING_FIXED_AMOUNT = 'FREE_SHIPPING_FIXED_AMOUNT';
}

