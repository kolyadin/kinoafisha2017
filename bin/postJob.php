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

$projectId = 2;
$params = [
    ['type' => 'text', 'key' => 'movie_name', 'value' => 'Аватар'],
    ['type' => 'number', 'key' => 'movie_year', 'value' => 2009],
    ['type' => 'image_url', 'key' => 'movie_images', 'value' => [
        'http://s1.picswalls.com/wallpapers/2015/09/20/2015-wallpaper_111525594_269.jpg',
        'http://wonderwordz.com/wp-content/uploads/2017/02/9-1.jpg',
        'http://hdlatestwallpaper.com/wp-content/uploads/2017/05/Switzerland-Architecture-HD-wallpaper.jpg'
    ]]
];

try {
    $response = $client->post("/public/api/projects/$projectId/jobs", [
        'json' => [
            'params' => $params,
            //'verify' => false
        ]]);

    if (202 !== $response->getStatusCode()) {
        throw new \RuntimeException("Unexpected response code status received, expected [ 202 ], received [ {$response->getStatusCode()} ]");
    }

    //Render job successfully accepted by platform and placed in queue
    //Please check periodically this location for get job status:
    $checkLocationUrl = $response->getHeader('location')[0];

    $responseBody = json_decode($response->getBody()->getContents(), true);

    print_r($responseBody);
} catch (ConnectException $e) {

    echo 'Network problem, please retry';

} catch (ClientException $e) {

    switch ($e->getCode()) {
        case 400:
            //Problems with params or json body or other client staff
            //Please check $e->getMessage() method for clarification
            echo $e->getMessage();
            break;
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


//dump($out);

//$out->getBody()->getContents();
//$out->getHeader('X-Websocket-Monitor-Channel');