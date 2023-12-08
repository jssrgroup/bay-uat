<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Payment;

class PaymentController extends BaseController
{

    public function getToken()
    {

        $curl = curl_init();

        $grant_type = 'client_credentials';
        $client_id = env('API_KEY', false);
        $client_secret = env('API_SECRET', false);

        $header = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . '/auth/oauth/v2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => "grant_type=$grant_type&client_id=$client_id&client_secret=$client_secret",
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response);
            return response()->json($data, 200);
        }
    }

    public function initiation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accountTo' => 'required|string',
            'bankCode' => 'required|string',
            'amount' => 'required|numeric',
            // 'remark' => 'string|null',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = array(
            "amountDirectionCode" => "DBCR",
            "transactionCode" => ($validator->validated()['bankCode'] == "025") ? "DIRECT_CREDIT" : "SMART_CREDIT",
            "annotation" => $request['remark'],
            "accountFrom" => [
                "accountNumber" => "7770083525"
            ],
            "accountTo" => [
                "accountNumber" => $validator->validated()['accountTo'],
                "bankCode" => $validator->validated()['bankCode']
            ],
            "transaction" => [
                "amount" => $validator->validated()['amount']
            ],
        );

        $token = $this->getToken()->original->access_token;
        $signatureKey = env('SIGNATURE_KEY', false);

        $url = '/rest/api/v1/accounts/deposits/fundTransfer/initiation';
        $uuid = Str::uuid();
        $datetime = $this->getDateTime();
        $timestamp = $datetime['timestamp'];
        $digest = base64_encode(hash("sha256", json_encode($data), TRUE));
        $signatureValue = "(request-target): post $url\n(created): $timestamp\ndigest: SHA-256=$digest\nx-client-transaction-id: $uuid";
        $signature = base64_encode(hash_hmac("sha256", $signatureValue, $signatureKey, TRUE));
        $Signature = 'keyId="client-secret",algorithm="hs2019",created=' . $timestamp . ',headers="(request-target) (created) digest x-client-transaction-id",signature="' . $signature . '"';

        $header = array(
            'X-Client-Transaction-ID: ' . $uuid,
            'Authorization: Bearer ' . $token,
            'Signature: ' . $Signature,
            'Digest: SHA-256=' . $digest,
            'Content-Type: application/json',
            'Date: ' . $datetime['datetime'],
            'Content-Length: ' . strlen(json_encode($data)),
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $json = json_decode($response);

            $payment = new Payment();
            $payment->transactionReferenceNumber = $json->transactionReferenceNumber;
            $payment->transactionInitiationNumber = $json->transactionInitiationNumber;
            $payment->amountDirectionCode = $json->amountDirectionCode;
            $payment->transactionCode = $json->transactionCode;
            $payment->annotation = $json->annotation;
            $payment->accountFromAccountNumber = $json->accountFrom->accountNumber;
            $payment->accountFromBankCode = $json->accountFrom->bankCode;
            $payment->accountToAccountNumber = $json->accountTo->accountNumber;
            $payment->accountToBankCode = $json->accountTo->bankCode;
            $payment->accountToAccountNameTH = isset($json->accountTo->accountNameTH) ? $json->accountTo->accountNameTH : '';
            $payment->accountToAccountNameEN = isset($json->accountTo->accountNameEN) ? $json->accountTo->accountNameEN : '';
            $payment->transactionAmount = $json->transaction->amount;
            $payment->transactionCommunicationFee = $json->transaction->communicationFee;
            $payment->transactionTransactionFee = $json->transaction->transactionFee;
            $payment->transactionTransactionDateTime = isset($json->accountTo->transactionDateTime) ? $json->transaction->transactionDateTime : '';
            $payment->endToEndIdentification = isset($json->accountTo->endToEndIdentification) ? $json->transaction->endToEndIdentification : '';
            $payment->status = 0;
            $payment->save();
            return response()->json($payment, 200);
        }
    }

    public function confirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transactionInitiationNumber' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = array(
            "transactionInitiationNumber" => $validator->validated()['transactionInitiationNumber'],
        );

        $token = $this->getToken()->original->access_token;
        $signatureKey = env('SIGNATURE_KEY', false);

        $url = '/rest/api/v1/accounts/deposits/fundTransfer/confirmation';
        $uuid = Str::uuid();
        $datetime = $this->getDateTime();
        $timestamp = $datetime['timestamp'];
        $digest = base64_encode(hash("sha256", json_encode($data), TRUE));
        $signatureValue = "(request-target): post $url\n(created): $timestamp\ndigest: SHA-256=$digest\nx-client-transaction-id: $uuid";
        $signature = base64_encode(hash_hmac("sha256", $signatureValue, $signatureKey, TRUE));
        $Signature = 'keyId="client-secret",algorithm="hs2019",created=' . $timestamp . ',headers="(request-target) (created) digest x-client-transaction-id",signature="' . $signature . '"';

        $header = array(
            'X-Client-Transaction-ID: ' . $uuid,
            'Authorization: Bearer ' . $token,
            'Signature: ' . $Signature,
            'Digest: SHA-256=' . $digest,
            'Content-Type: application/json',
            'Date: ' . $datetime['datetime'],
            'Content-Length: ' . strlen(json_encode($data)),
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response);
            return response()->json($data, 200);
        }
    }

    public function inquire(Request $request, $transactionInitiationNumber)
    {
        $input['transactionInitiationNumber'] = $transactionInitiationNumber;
        $validator = Validator::make($input, [
            'transactionInitiationNumber' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = array(
            "transactionInitiationNumber" => $validator->validated()['transactionInitiationNumber'],
        );

        $token = $this->getToken()->original->access_token;
        $signatureKey = env('SIGNATURE_KEY', false);

        $url = "/rest/api/v1/accounts/deposits/fundTransfer/inquiry/{$validator->validated()['transactionInitiationNumber']}";
        $uuid = Str::uuid();

        $header = array(
            'X-Client-Transaction-ID: ' . $uuid,
            'Authorization: Bearer ' . $token,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response);
            return response()->json($data, 200);
        }
    }

    function getDateTime()
    {
        $datetime = new DateTime();
        $timezone = new DateTimeZone('Asia/Bangkok');
        $datetime->setTimezone($timezone);
        $iso8604 = $datetime->format(DateTime::COOKIE);
        return array('datetime' => $iso8604, 'timestamp' => strtotime($iso8604));
    }
}
