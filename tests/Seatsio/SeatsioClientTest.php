<?php

namespace Seatsio;

use GuzzleHttp\Client;

class SeatsioClientTest extends \PHPUnit_Framework_TestCase
{
    private static $BASE_URL = 'https://api-staging.seats.io/';

    /** @var SeatsioClient */
    protected $seatsioClient;

    protected function setUp()
    {
        $secretKey = $this->createTestAccount();
        $this->seatsioClient = new SeatsioClient($secretKey, self::$BASE_URL);
    }

    private function createTestAccount()
    {
        $client = new Client();
        $randomEmail = 'test' . rand() . '@seats.io';
        $requestBody = ['email' => $randomEmail, 'password' => '12345678'];
        $res = $client->request('POST', self::$BASE_URL . 'system/public/users', ['json' => $requestBody]);
        $body = \GuzzleHttp\json_decode($res->getBody());
        return $body->secretKey;
    }
}