<?php

namespace App\Enums;

enum TransactionType: string
{
    case IN = 'IN';
    case OUT ='OUT';
    case ADJ ='ADJ';
    case TRANSFER ='TRANSFER';
}
