<?php
namespace App\helper;
class SmsOtp
{

    public static function Send($number, $message)
    {

        $url = "http://bulksmsbd.net/api/smsapi";
        $api_key = "ZjvNGWfYlrvk6wUKhQQb";
        $senderid = "8809617614289";

        $data = [
            "api_key" => $api_key,
            "senderid" => $senderid,
            "number" => $number,
            "message" => $message
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = json_decode(curl_exec($ch));

        // return $response;

        if ($response && isset($response->response_code) && $response->response_code != 202) {
            $errorMessage = $response->error_message;

            return ['success' => false, 'error_message' => $errorMessage];
        }

        return ['success' => true];
    }
}
