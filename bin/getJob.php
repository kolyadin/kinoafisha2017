<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

require_once __DIR__ . '/../vendor/autoload.php';

$client = new Client([
    'base_uri' => 'https://dynamicmedia.agency/',
    'headers' => [
        'X-USE-CACHE' => 'no',
        'X-AUTHORIZE-TOKEN' => '__AUTHORIZE__TOKEN__'
    ]
]);

$jobId = 486;

try {
    $response = $client->get("/public/api/jobs/$jobId/monitor");

    if (200 !== $response->getStatusCode()) {
        throw new \RuntimeException("Unexpected response code status received, expected [ 200 ], received [ {$response->getStatusCode()} ]");
    }

    $responseBody = json_decode($response->getBody()->getContents(), true);

    if ($responseBody['status'] == 'ready') {
        print_r($responseBody);
    }

} catch (ConnectException $e) {

    echo 'Network problem, please retry';

} catch (ClientException $e) {

    switch ($e->getCode()) {
        case 401:
            //Wrong X-AUTHORIZE-TOKEN header
            //Recheck it or contact technical team
            echo $e->getMessage();
            break;
        case 403:
            //Problem with user rights
            //If you received this, contact technical team
            echo $e->getMessage();
            break;
        case 404:
            echo $e->getMessage();
            //Wrong project id provided or project no exists
            break;
    }

} catch (\Exception $e) {

    //Some unexpected exception catched

    echo $e->getMessage();

}