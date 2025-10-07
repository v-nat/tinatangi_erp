<?php

namespace App\Enums;

enum transaction_type: string
{
    case in = 'IN';
    case out ='OUT';
    case adj ='ADJ';
    case transfer ='TRANSFER';
}
