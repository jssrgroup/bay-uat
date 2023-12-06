<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;

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
            CURLOPT_URL => env('BAY_URL', false) . 'auth/oauth/v2/token',
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
}
