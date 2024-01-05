<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionReferenceNumber',
        'transactionInitiationNumber',
        'amountDirectionCode',
        'transactionCode',
        'annotation',
        'accountFromAccountNumber',
        'accountFromBankCode',
        'accountToAccountNumber',
        'accountToBankCode',
        'accountToAccountNameTH',
        'accountToAccountNameEN',
        'transactionAmount',
        'transactionCommunicationFee',
        'transactionTransactionFee',
        'transactionTransactionDateTime',
        'endToEndIdentification',
        'status',
        'data',
        'fileName'
    ];
}
