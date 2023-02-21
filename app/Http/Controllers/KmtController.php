<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Validator;

class KmtController extends Controller
{
    public function callback(Request $request)
    {
        return response()->json(["status" => 200, "data" => [
            "user" => $request->all()
        ]]);
    }
    public function qrCode(Request $request)
    {
        // QR code with text
        // $qrcode = QrCode::size(200)->format('png')->generate('Welcome to Makitweb', public_path('images/qrcode.svg') );
        // $qrcode = base64_encode(QrCode::size(200)->format('png')->generate('Welcome to Makitweb'));
        // return response()->json(["status" => 200, "data" => [
        //     "qrcode" => $qrcode
        // ]]);
        $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'reference1' => 'required|string|min:6',
            'reference2' => 'required|string|min:6',
            'remark' => 'required|string',
            'terminalId' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'amount' => $validator->validated()['amount'],
            'billerId' => env('BILLER_ID', false),
            'bizMchId' => env('BIZMCH_ID', false),
            'channel' => env('CHANNEL', false),
            'reference1' => $validator->validated()['reference1'],
            'reference2' => $validator->validated()['reference2'],
            'remark' => $validator->validated()['remark'],
            'terminalId' => $validator->validated()['terminalId'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        // return response()->json(["status" => 200, "data" => [
        //     "stringA" => $stringA,
        //     "stringB" => $stringB,
        //     "sign" => base64_encode($encrypted_message),
        //     "payload" => $data,
        // ]]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payment.jssr.co.th/KrungsriAPI/apix/qrcode.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response, true);
            return response()->json(array(
                'trxId'=>$data['trxId'],
                'qrcodeContent'=>$data['qrcodeContent'],
                'qrcode'=>base64_encode(QrCode::size(200)->format('png')->generate($data['qrcodeContent'])),
            ), 200);
        }
    }

    public function transection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trxId' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'bizMchId' => env('BIZMCH_ID', false),
            'trxId' => $validator->validated()['trxId'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        // return response()->json(["status" => 200, "data" => [
        //     "stringA" => $stringA,
        //     "stringB" => $stringB,
        //     "sign" => base64_encode($encrypted_message),
        //     "payload" => $data,
        // ]]);
        // exit;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payment.jssr.co.th/KrungsriAPI/apix/transection.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response, true);
            return response()->json($data, 200);
        }
    }
    public function transectionList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numberPerPage' => 'required',
            'pageNumber' => 'required',
            'timeStart' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'billerId' => env('BILLER_ID', false),
            'bizMchId' => env('BIZMCH_ID', false),
            'numberPerPage' => $validator->validated()['numberPerPage'],
            'pageNumber' => $validator->validated()['pageNumber'],
            'timeStart' => $validator->validated()['timeStart'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        // return response()->json(["status" => 200, "data" => [
        //     "stringA" => $stringA,
        //     "stringB" => $stringB,
        //     "sign" => base64_encode($encrypted_message),
        //     "payload" => $data,
        // ]]);
        // exit;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://payment.jssr.co.th/KrungsriAPI/apix/transectionList.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: */*",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json($err, 422);
        } else {
            $data = json_decode($response, true);
            return response()->json($data, 200);
        }
    }
    public function getSign(Request $request)
    {
        $data = $request->data;

        openssl_public_encrypt($data, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);

        return response()->json(["status" => 200, "data" => [
            "data" => $data,
            "sign" => base64_encode($encrypted_message)
        ]]);
    }
}
