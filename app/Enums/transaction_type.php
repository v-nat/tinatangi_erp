<?php

namespace App\Enums;

enum transaction_type: string
{
    case In = 'IN';
    case Out ='OUT';
    case Adj ='ADJ';
    case Transfer ='TRANSFER';
}
