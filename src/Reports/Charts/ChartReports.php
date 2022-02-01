<?php

namespace Seatsio\Reports\Charts;

use GuzzleHttp\Client;
use Seatsio\SeatsioJsonMapper;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\Charts\ChartObjectInfo;
use function Symfony\Component\String\b;

class ChartReports
{

    /**
     * @var Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function byLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $objectType string
     * @return array
     */
    public function byObjectType($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byObjectType', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function summaryByObjectType($chartKey, $bookWholeTables = null)
    {
        return $this->getChartSummaryReport('byObjectType', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function byCategoryKey($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryKey', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function summaryByCategoryKey($chartKey, $bookWholeTables = null)
    {
        return $this->getChartSummaryReport('byCategoryKey', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function byCategoryLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function summaryByCategoryLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartSummaryReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function bySection($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('bySection', $chartKey, $bookWholeTables);
    }

    /**
     * @param $chartKey string
     * @param $bookWholeTables string
     * @return array
     */
    public function summaryBySection($chartKey, $bookWholeTables = null)
    {
        return $this->getChartSummaryReport('bySection', $chartKey, $bookWholeTables);
    }

    private static function reportUrl($reportType, $chartKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}', array("key" => $chartKey, "reportType" => $reportType));
    }

    private static function summaryReportUrl($reportType, $chartKey)
    {
        return UriTemplate::expand('/reports/charts/{key}/{reportType}/summary', array("key" => $chartKey, "reportType" => $reportType));
    }

    private function getChartReport($reportType, $chartKey, $bookWholeTables)
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    private function getChartSummaryReport($reportType, $chartKey, $bookWholeTables)
    {
        $res = $this->client->get(self::summaryReportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        return \GuzzleHttp\json_decode($res->getBody(), true);
    }

    private static function mapMultiValuedReport($json)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItems) {
            $result[$status] = $mapper->mapArray($reportItems, array(), ChartObjectInfo::class);
        }
        return $result;
    }

}
