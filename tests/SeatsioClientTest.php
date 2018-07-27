<?php

namespace Seatsio;

use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;

class SeatsioClientTest extends PHPUnit_Framework_TestCase
{
    private static $BASE_URL = 'https://api-staging.seatsio.net/';

    /** @var SeatsioClient */
    protected $seatsioClient;

    private $user;

    protected function setUp()
    {
        $this->user = $this->createTestAccount();
        $this->seatsioClient = self::createSeatsioClient($this->user->secretKey);
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
        $res = $client->post(self::$BASE_URL . 'system/public/users/actions/create-test-user');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    protected function randomEmail()
    {
        return self::uuid() . '@mailinator.com';
    }

    protected function createTestChart()
    {
        return $this->createTestChartFromFile('sampleChart.json');
    }

    protected function createTestChartWithSections()
    {
        return $this->createTestChartFromFile('sampleChartWithSections.json');
    }

    private function createTestChartFromFile($file)
    {
        $client = new Client();
        $requestBody = file_get_contents(dirname(__FILE__) . '/' . $file);
        $chartKey = self::uuid();
        $client->post(
            self::$BASE_URL . 'system/public/' . $this->user->designerKey . '/charts/' . $chartKey,
            ['body' => $requestBody]
        );
        return $chartKey;
    }

    private static function uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}