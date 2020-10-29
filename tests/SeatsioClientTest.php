<?php

namespace Seatsio;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Seatsio\Subaccounts\Subaccount;

class SeatsioClientTest extends TestCase
{
    private static $BASE_URL = 'https://api-staging.seatsio.net/';

    /** @var SeatsioClient */
    protected $seatsioClient;

    protected $user;

    /** @var Subaccount */
    protected $subaccount;

    protected function setUp()
    {
        $company = $this->createTestCompany();
        $this->user = $company->admin;
        $this->subaccount = $company->subaccount;
        $this->seatsioClient = self::createSeatsioClient($this->user->secretKey);
    }

    /**
     * @return SeatsioClient
     */
    protected static function createSeatsioClient($secretKey, $workspaceKey = null)
    {
        return new SeatsioClient($secretKey, $workspaceKey, self::$BASE_URL);
    }

    private function createTestCompany()
    {
        $client = new Client();
        $res = $client->post(self::$BASE_URL . 'system/public/users/actions/create-test-company');
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

    protected function createTestChartWithTables()
    {
        return $this->createTestChartFromFile('sampleChartWithTables.json');
    }

    protected function createTestChartWithSections()
    {
        return $this->createTestChartFromFile('sampleChartWithSections.json');
    }

    protected function createTestChartWithErrors()
    {
        return $this->createTestChartFromFile('sampleChartWithErrors.json');
    }

    private function createTestChartFromFile($file)
    {
        $client = new Client();
        $requestBody = file_get_contents(dirname(__FILE__) . '/' . $file);
        $chartKey = self::uuid();
        $client->post(
            self::$BASE_URL . 'system/public/charts/' . $chartKey,
            [
                'body' => $requestBody,
                'auth' => [$this->user->secretKey, null]
            ]
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
