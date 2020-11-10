<?php

namespace Seatsio\Reports\Usage;

use GuzzleHttp\Client;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObject;
use Seatsio\Reports\Usage\DetailsForMonth\UsageDetails;
use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\Reports\Usage\SummaryForMonths\UsageSummaryForMonth;
use Seatsio\SeatsioJsonMapper;

class UsageReports
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
     * @return UsageSummaryForMonth[]
     */
    public function summaryForAllMonths()
    {
        $res = $this->client->get('/reports/usage');
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json, array(), UsageSummaryForMonth::class);
    }

    /**
     * @param Month $month
     * @return UsageDetails[]
     */
    public function detailsForMonth($month)
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize());
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json, array(), UsageDetails::class);
    }

    /**
     * @param int $eventId
     * @param Month $month
     * @return UsageForObject[]
     */
    public function detailsForEventInMonth($eventId, $month)
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize() . '/event/' . $eventId);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json, array(), UsageForObject::class);
    }

}
