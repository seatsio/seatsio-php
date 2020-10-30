<?php

namespace Seatsio\Reports\Charts;

use Seatsio\SeatsioJsonMapper;

class ChartReports
{

    /**
     * @var \GuzzleHttp\Client
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
    public function byCategoryLabel($chartKey, $bookWholeTables = null)
    {
        return $this->getChartReport('byCategoryLabel', $chartKey, $bookWholeTables);
    }

    private static function reportUrl($reportType, $eventKey)
    {
        return \GuzzleHttp\uri_template('/reports/charts/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
    }

    private function getChartReport($reportType, $chartKey, $bookWholeTables)
    {
        $res = $this->client->get(self::reportUrl($reportType, $chartKey), ["query" => ["bookWholeTables" => $bookWholeTables]]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    private static function mapMultiValuedReport($json)
    {
        $mapper = SeatsioJsonMapper::create();
        $result = [];
        foreach ($json as $status => $reportItems) {
            $result[$status] = $mapper->mapArray($reportItems, array(), ChartReportItem::class);
        }
        return $result;
    }

}
