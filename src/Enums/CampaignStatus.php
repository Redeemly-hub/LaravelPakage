<?php

namespace LuckyCode\IntegrationHelper\Enums;

enum CampaignStatus: int
{
    case Pending = 1;
    case Running = 2;
    case Deactivated = 3;
    case RunningOutOfVoucher = 4;
    case Stopped = 5;
    case Closed = 6;
    case Disable = 7;
}

