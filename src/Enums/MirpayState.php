<?php

namespace TurgunboyevUz\Mirpay\Enums;

enum MirpayState: int
{
    case PENDING = 0;
    case SUCCESS = 1;
    case FAILED = 2;
}