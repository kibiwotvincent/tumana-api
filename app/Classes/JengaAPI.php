<?php

namespace App\Classes;

use Spatie\Crypto\Rsa\PrivateKey;
use Illuminate\Support\Facades\Http;

class JengaAPI
{
    public static function getSignature($data)
    {
        $dataString = implode('', $data);
        $privateKey = config(key: 'jenga.keys_path').'/jenga.key';

        return PrivateKey::fromFile($privateKey)->sign($dataString);
    }
    
    public static function getToken()
    {
        $url = config(key: 'jenga.host') . "/authentication/api/v3/authenticate/merchant";
        $apiKey = config(key: 'jenga.key');
        $merchantCode = config(key: 'jenga.merchant');
        $consumerSecret = config(key: 'jenga.secret');

        $response = Http::acceptJson()
                    ->withHeaders(headers: ['Api-Key' => $apiKey])
                    ->retry(3, 100)
                    ->post(url: $url, data: [
                        'merchantCode'   => $merchantCode,
                        'consumerSecret' => $consumerSecret
                    ]);

        if (! $response->successful()) {
            //$this->error('Connection error. Cannot fetch access token.');

            return '';
        }
        
        $data = $response->json();
        
        return $data['accessToken'];
    }
}
