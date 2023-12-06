<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transactionReferenceNumber');
            $table->string('transactionInitiationNumber')->unique();
            $table->string('amountDirectionCode');
            $table->string('transactionCode');
            $table->string('annotation');
            $table->string('accountFromAccountNumber');
            $table->string('accountFromBankCode');
            $table->string('accountToAccountNumber');
            $table->string('accountToBankCode');
            $table->string('accountToAccountNameTH');
            $table->string('accountToAccountNameEN');
            $table->string('transactionAmount');
            $table->string('transactionCommunicationFee');
            $table->string('transactionTransactionFee');
            $table->string('transactionTransactionDateTime')->nullable();
            $table->string('endToEndIdentification')->nullable();
            $table->binary('status');
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
