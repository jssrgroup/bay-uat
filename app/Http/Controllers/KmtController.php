<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Transection as TransectionResource;
use App\Models\Callback;
use App\firebaseRDB;
use App\Models\Log;
use App\Models\Qrcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KmtController extends BaseController
{
    public function header(Request $request)
    {
        $PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCLy7wi6vVA/k5StJziTUasUPlJ+nVZuAD8Om9sRNuOaJryBUAVY7LwoFIU+aMqVVw1Jl5PENxqJeQf+RtCR7BWn2j1cjX0ch3xHFq9a1ixoqKDJBNq/KmRs5SjLqWSHwU59zv0KNdtKr8pv+JN+cln/2JzazM/KVQ0GsoOhGxxTQIDAQAB\n-----END PUBLIC KEY-----";
        // $PRIVATE_KEY = "-----BEGIN PRIVATE KEY-----\nXXXMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCLy7wi6vVA/k5StJziTUasUPlJ+nVZuAD8Om9sRNuOaJryBUAVY7LwoFIU+aMqVVw1Jl5PENxqJeQf+RtCR7BWn2j1cjX0ch3xHFq9a1ixoqKDJBNq/KmRs5SjLqWSHwU59zv0KNdtKr8pv+JN+cln/2JzazM/KVQ0GsoOhGxxTQIDAQAB\n-----END PRIVATE KEY-----";
        $PRIVATE_KEY = openssl_get_privatekey($PUBLIC_KEY);
        $PUB = "-----BEGIN CERTIFICATE-----\nMIID7zCCAtegAwIBAgIBADANBgkqhkiG9w0BAQUFADCBkTELMAkGA1UEBhMCVEgx\nFDASBgNVBAgMC1NhbXV0cHJha2FuMQ8wDQYDVQQHDAZCYW5nYm8xDTALBgNVBAoM\nBEpTU1IxDDAKBgNVBAsMAzMwMDEbMBkGA1UEAwwSaHR0cHM6Ly9qc3NyLmNvLnRo\nMSEwHwYJKoZIhvcNAQkBFhJqc3NyLmRldkBnbWFpbC5jb20wHhcNMjMwMjI0MTY1\nODE0WhcNMjQwMjI0MTY1ODE0WjCBkTELMAkGA1UEBhMCVEgxFDASBgNVBAgMC1Nh\nbXV0cHJha2FuMQ8wDQYDVQQHDAZCYW5nYm8xDTALBgNVBAoMBEpTU1IxDDAKBgNV\nBAsMAzMwMDEbMBkGA1UEAwwSaHR0cHM6Ly9qc3NyLmNvLnRoMSEwHwYJKoZIhvcN\nAQkBFhJqc3NyLmRldkBnbWFpbC5jb20wggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAw\nggEKAoIBAQCrqV1NKcWa2qME1uwNoUoR0hPTlgAPJvQKsEU2sbF9qCanWXq+GNn0\n6H1ZM59RVIimF0WdAoj44nvFDnM4BEKkH4yJoQzXoEyntDjyIJ33vAwW19rMCd46\nlCf8a+thhQOk/NgsfIxP5NXkiRdfmSs1CZZTkIb3gIK/eSYNqy/D37LMY4pWWwLJ\npz0tquf1qL27LUG7BZK5leEDhfTh3LZiAUr1ft4XWmrr7GEPhgEPgSj95KL/Osgh\n0U1/b9Y95/kzhyywjzklQjAEqUiZloLa10MGBnX+IoQiHX11jZS2uN6qVQfIGJ3e\nYmPUvHM+iV/fP1CtmL+WZEKPTPWHgfGVAgMBAAGjUDBOMB0GA1UdDgQWBBR+0dPC\nxxyFha+GFrCLGoqIUAYySDAfBgNVHSMEGDAWgBR+0dPCxxyFha+GFrCLGoqIUAYy\nSDAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4IBAQCG4po05Ovtk+gfSHYA\n9W2oVPiI3BYjqkk4t2nXPwPwjX8RC0tOhD3u4lDDP0wxDIPmlcK3rP7QMGnTBrON\nCmCLo5E4m3r27E6e02h62IFJZtXwHoDrwNf2UTB2Q1TWwA5439dSMFdN5nZjcZf1\n7o+fYFhJMmNpDHvv4j3Eakg7F5da+GTkI/Bk9NU8hs9fctQjtWi4RjTu6YrqdnAg\nI3DLg4w7mXUjUApS+WGkmhttYL4V0D8S2MWSxUxPv6lGs9thp5NoMDInaTUxI4H+\nEU1I3U6h9EyyEQ2/yEYvqcVPCDTeDrf2Dd+LJQUzJZoihzhZEQcyXPKlz940qFCF\n9j8T\n-----END CERTIFICATE-----";
        $PRI = "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCrqV1NKcWa2qME\n1uwNoUoR0hPTlgAPJvQKsEU2sbF9qCanWXq+GNn06H1ZM59RVIimF0WdAoj44nvF\nDnM4BEKkH4yJoQzXoEyntDjyIJ33vAwW19rMCd46lCf8a+thhQOk/NgsfIxP5NXk\niRdfmSs1CZZTkIb3gIK/eSYNqy/D37LMY4pWWwLJpz0tquf1qL27LUG7BZK5leED\nhfTh3LZiAUr1ft4XWmrr7GEPhgEPgSj95KL/Osgh0U1/b9Y95/kzhyywjzklQjAE\nqUiZloLa10MGBnX+IoQiHX11jZS2uN6qVQfIGJ3eYmPUvHM+iV/fP1CtmL+WZEKP\nTPWHgfGVAgMBAAECggEAFdBH0u5j9fGilZLaHEFKsEkJy/FbwJEqodEUeobKDzW2\n870jI9T0nXKNuzAcJrGfS7vZVMJltXrST5AuhAfGkCSSL0sP3ra4BveX5OzQJhL3\nVk/HtfXrD3aUPjdYiePlOqoGrmCFVAG4THo/Rh6opuy49ZWAESN7eeljNIl0YGdv\nbWEXRZwdbolktOCrrUG2SXlWYC72xg3iJIIHI+ZW8OX35sDOkZgyCQFJELqQ/Xy7\niKdvph/1AHcJJOASV9rENS9FYtLXm3FK/cJDVGpukWorBQTW9bNQ9NHY+aGaGSK0\nPJgjDPoRaKQbnr9gxdz4Ngjly+YvXEu7SNL1oz+WYQKBgQDddzGX5XqvcOay4tmx\nfjcz64BkL78CJjzg3oAS7D9rR2jLXfD0fhiH6cXqRLSo4NTBfpU+E4ZhyWc0hQ/2\nmlyOzgyhW4KYxNd8yZkZ4wJv4xGrITLzGAaDMoqGPWGN+nhL20QXQRsu/mtAJDpa\nMpoAeaZoSGTNwva2NVCjpBoaxwKBgQDGbgRPMRRd1vGfvX7QOolIi5FTyJdB+UKd\ntEO9348wUoOSIve9QnyMp4zyrDZdGVlAuQJpqwpf8ERsOUq/q/6gQPnZtynQ4AUv\nM8Uk6sgd6ng9LOJj1c8So0DlpwBiYN6IgHvsrmziIHjaUX3ulnS4FKgJclwsxRHV\nfq5pTjgUwwKBgQCQYNVnBkbOO1dbBcMQEI2ifoRsTChFGXKU0tlB/Nx3bs1lz293\npQEPEhth17rDYhexsXlQR+BSvb9XoOvAQ+/VdYUMaKEiQSmOg8sY8kKzeKAbJYCW\ntHICdB+U1k1UBxcHtQGV+27iWyDGZxfXl6eIacZPs+prrg+yx90zYZdXHwKBgE7P\nMTz2pD0k+nBURcKUDSRX8k9RcunMAu45w1HtMaSikQkAhF6Rwn/mp+9WAH13bOBs\n2o13VjaGadaF5q87s3SYeeNDgQMphkWaDSm9ad+f2UOKyRuXtpeTaVoMuvVXvep/\nBTkaibpB5V3oUdjpvs/BvH+MARny50Ng/gpq+5EbAoGABv0yFZgPz1CVCLSuOjW0\nkcekPflpqyRINuyLZE6kInZrn8WEm4OT0RfxWwgD5A0ZYxVAeEU9BZqDbIrX2Mef\ntBUqlo8KeHiNn35xSrnsCQUIOf2vdp/wLxL/b15oDsx3E2njgyOTmJhJkwRmtEQ3\nwC1Ki9J5FP5hb/+k4WrT+vM=\n-----END PRIVATE KEY-----";
        $input = $request->header();
        $apiKey = $input['api-key'][0];
        openssl_public_encrypt($apiKey, $crypted, $PUB, OPENSSL_PKCS1_PADDING);
        openssl_private_decrypt($crypted, $decrypted, $PRI, OPENSSL_PKCS1_PADDING);
        // $privkey = openssl_pkey_new();
        // openssl_pkey_export_to_file($privkey, 'D:/privatekey.pem');

        return response()->json(array(
            'api_key' => $apiKey,
            'encrypted_message' => base64_encode($crypted),
            'decrypted_message' => $decrypted,
            // 'privkey' => $PRIVATE_KEY,

        ));
    }

    public function callback(Request $request)
    {
        $input = $request->all();

        $callback = [
            'trxId' => $input['trxId'],
            'terminalId' => $input['terminalId'],
            'data' => json_encode($input)
        ];

        $db = new firebaseRDB(env('FIREBASE_DATABASE_URL', false));
        $insert = $db->insert("callback/{$callback['terminalId']}", $callback);

        $cb = Callback::create($callback);

        $data = [
            'message' => 'Successful reception',
            'returnCode' => 10000,

        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        return response()->json($data);
    }

    public function qrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|string',
            'reference1' => 'required|string',
            'reference2' => 'required|string',
            'remark' => 'required|string',
            'terminalId' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation error.', $validator->errors(), 422);
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
        $stringB = hash("sha256", mb_convert_encoding($stringA, 'UTF-8'));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        $curl = curl_init();

        $header = array(
            'API-Key: ' . env('API_KEY', false),
            'X-Client-Transaction-ID: ' . Str::uuid(),
            'Content-Type: application/json',
        );

        curl_setopt_array($curl, array(//env('BAY_URL', false) . 'trans/precreate', //
            CURLOPT_URL => 'https://payment.jssr.co.th/KrungsriAPI/apix/qrcode.php', //
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $responseArr = json_decode($response, true);

        // return response()->json($responseArr, 500);

        if ($err || $response == "File not found.\n") {
            return $this->sendError($response, 'Curl error.', 500);
        } else {
            if (isset($responseArr['returnCode'])) {
                if ($responseArr['returnCode'] == '10000') {
                    $payload = [
                        'header' => $header,
                        'body' => $data,
                    ];
                    $log = [
                        'trxId' => $responseArr['trxId'],
                        'payload' => json_encode($payload),
                    ];
                    Log::create($log);

                    $res = [
                        'trxId' => $responseArr['trxId'],
                        'terminalId' => $data['terminalId'],
                        'qrcodeContent' => $responseArr['qrcodeContent'], //
                        'qrcode' => $responseArr['qrcodeContent'], //base64_encode(QrCode::size(200)->format('png')->generate($responseArr['qrcodeContent'])),
                    ];

                    Qrcode::create($res);

                    return $this->sendResponse($res, $responseArr['message']);
                } else {
                    return $this->sendError($responseArr['message'], $responseArr, 500);
                }
            } else {
                return $this->sendError('response error', $responseArr, 500);
            }
        }
    }

    public function transection(Request $request, $id)
    {
        $input['trxId'] = $id;
        $validator = Validator::make($input, [
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

        $curl = curl_init();

        $header = array(
            'API-Key: ' . env('API_KEY', false),
            'X-Client-Transaction-ID: ' . Str::uuid(),
            'Content-Type: application/json',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . 'trans/detail',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
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
            return $this->sendResponse(new TransectionResource($data->transaction), 'Transection retrived successfully.');
        }
    }

    public function transectionList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numberPerPage' => 'required',
            'pageNumber' => 'required',
            // 'timeStart' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'billerId' => env('BILLER_ID', false),
            'bizMchId' => env('BIZMCH_ID', false),
            'numberPerPage' => $validator->validated()['numberPerPage'],
            'pageNumber' => $validator->validated()['pageNumber'],
            // 'timeStart' => $validator->validated()['timeStart'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        $curl = curl_init();


        $header = array(
            'API-Key: ' . env('API_KEY', false),
            'X-Client-Transaction-ID: ' . Str::uuid(),
            'Content-Type: application/json',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . 'trans/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
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
            return $this->sendResponse(TransectionResource::collection($data->data->transactions), 'Transection list retrived successfully.');
        }
    }

    public function settleList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numberPerPage' => 'required',
            'pageNumber' => 'required',
            // 'timeStart' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'bizMchId' => env('BIZMCH_ID', false),
            'numberPerPage' => $validator->validated()['numberPerPage'],
            'pageNumber' => $validator->validated()['pageNumber'],
            // 'timeStart' => $validator->validated()['timeStart'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        $curl = curl_init();


        $header = array(
            'API-Key: ' . env('API_KEY', false),
            'X-Client-Transaction-ID: ' . Str::uuid(),
            'Content-Type: application/json',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . 'trans/settle/list',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
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
            return $this->sendResponse($data, 'Settle list retrived successfully.');
        }
    }

    
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trxQr' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'bizMchId' => env('BIZMCH_ID', false),
            'trxQr' => $validator->validated()['trxQr'],
        ];
        $stringA = '';
        foreach ($data as $key => $value) {
            $stringA .= "$key=$value&";
        }

        $stringA = substr($stringA, 0, -1);
        $stringB = hash("sha256", utf8_encode($stringA));
        openssl_public_encrypt($stringB, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);
        $data['sign'] = base64_encode($encrypted_message);

        $curl = curl_init();


        $header = array(
            'API-Key: ' . env('API_KEY', false),
            'X-Client-Transaction-ID: ' . Str::uuid(),
            'Content-Type: application/json',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('BAY_URL', false) . 'trans/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
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
            return $this->sendResponse($data, 'Verify check successfully.');
        }
    }

    public function getSign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated()['data'];

        openssl_public_encrypt($data, $encrypted_message, env('PUBLIC_KEY', false), OPENSSL_PKCS1_PADDING);

        return response()->json(["status" => 200, "data" => [
            "data" => $data,
            "sign" => base64_encode($encrypted_message)
        ]]);
    }
}
