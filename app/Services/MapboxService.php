<?php

namespace App\Services;

use GuzzleHttp\Client;

class MapboxService
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->accessToken = config('mapbox.mapbox_token');
    }

    public function getCoordinates($address)
    {
        $baseUrl = 'https://api.mapbox.com/geocoding/v5/mapbox.places/';
        $url = $baseUrl . $address . '.json?access_token=' . $this->accessToken;
        $response = $this->client->get($url);
        $a = json_decode($response->getBody(), true);
        $latitude = $a['features'][0]['center'][1];
        $longitude = $a['features'][0]['center'][0];
        return [$longitude, $latitude];
    }
    public function getDirections($address1, $address2)
    {
        $baseUrl = 'https://api.mapbox.com/directions/v5/mapbox/driving/';
        $coordinates1 = implode(',', $address1);
        $coordinates2 = implode(',', $address2);
        $url = $baseUrl . $coordinates1 . ';' . $coordinates2 . '.json?access_token=' . $this->accessToken;
        $response = $this->client->get($url);
        $a = json_decode($response->getBody(), true);
        $distance = $a['routes'][0]['distance'] / 1000;
        return $distance;
    }
}
