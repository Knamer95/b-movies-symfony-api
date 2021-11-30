<?php

namespace App\Services;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestService {
    private $client;

    public function __construct() {
        $this->client = new \Symfony\Component\HttpClient\CurlHttpClient();;
    }

    function sendRequest(String $url = "", String $method = "GET", Array $query_params = [], Array $body_params = []): Array {

        if (sizeof($query_params) > 0) $url .= "?" . http_build_query($query_params);

        $cURL = new CurlRequest($url);
        $cURL->setOption(CURLOPT_CUSTOMREQUEST, $method);
        $cURL->setOption(CURLOPT_URL, $url);
        $cURL->setOption(CURLOPT_RETURNTRANSFER, true);

        if ($method === "POST") {
            // Without this, the request from the axios call won't work (themoviedb returns an error of incorrect data ($body_params are not passed?)) why??
            // If there were POST requests with no params, this would need a change
            if (!http_build_query($body_params)) die();

            $cURL->setOption(CURLOPT_POST, 1);
            $cURL->setOption(CURLOPT_POSTFIELDS, http_build_query($body_params));
        }

        $result = $cURL->execute();
        $httpCode = $cURL->getInfo(CURLINFO_HTTP_CODE);
        $cURL->close();
        
        return [
            "status" => $httpCode ?: 500,
            "result" => json_decode($result, true)
        ];
    }
}


/*
// Need to investigate further on why this won't work on the POST request... could be related to the issue that the other one is facing that is fixed with a "hack"

function sendRequest(String $url = "", String $method = "GET", Array $query_params = [], Array $body_params = []): Array {
    $url .= "?" . http_build_query($query_params);

    $httpCode = 200;
    $content = "Default response";

    try {

        $response = $this->client->request(
            $method,
            $url,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                // 'query' => [$query_params],
                'body' => $body_params
            ]
        );

        $httpCode = $response->getStatusCode();
        $content = $response->getContent();
        $content = $response->toArray();
    } catch (Exception $e) {
        $result = json_decode($e, true);
        $httpCode = $e['code'];
        $content = $e['message'];
    }
    
    return [
        "status" => $httpCode ?: 500,
        "result" => $content
    ];
}
*/