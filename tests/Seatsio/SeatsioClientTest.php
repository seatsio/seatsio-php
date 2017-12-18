<?php

namespace Seatsio;

use GuzzleHttp\Client;

if (!class_exists('\PHPUnit\Framework\TestCase') && class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', 'PHPUnit\Framework\TestCase');
}

class SeatsioClientTest extends \PHPUnit_Framework_TestCase
{
    private static $BASE_URL = 'https://api-staging.seats.io/';

    /** @var SeatsioClient */
    protected $seatsioClient;

    protected function setUp()
    {
        $secretKey = $this->createTestAccount();
        $this->seatsioClient = self::createSeatsioClient($secretKey);
    }

    /**
     * @return SeatsioClient
     */
    protected static function createSeatsioClient($secretKey)
    {
        return new SeatsioClient($secretKey, self::$BASE_URL);
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