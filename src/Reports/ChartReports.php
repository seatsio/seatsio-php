<?php

namespace Seatsio\Reports;

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
     * @param $label string
     * @return array
     */
    public function byLabel($chartKey)
    {
        $res = $this->client->get(self::reportUrl('byLabel', $chartKey));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    private static function reportUrl($reportType, $eventKey)
    {
        return \GuzzleHttp\uri_template('/reports/charts/{key}/{reportType}', array("key" => $eventKey, "reportType" => $reportType));
    }

    public function byCategoryKey($chartKey)
    {
        $res = $this->client->get(self::reportUrl('byCategoryKey', $chartKey));
        $json = \GuzzleHttp\json_decode($res->getBody());
        return $this->mapMultiValuedReport($json);
    }

    /**
     * @param $json mixed
     * @return array
     */
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
