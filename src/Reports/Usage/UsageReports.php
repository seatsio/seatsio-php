<?php

namespace Seatsio\Reports\Usage;

use GuzzleHttp\Client;
use GuzzleHttp\Utils;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV1;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV2;
use Seatsio\Reports\Usage\DetailsForMonth\UsageDetails;
use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\Reports\Usage\SummaryForMonths\UsageSummaryForAllMonths;
use Seatsio\Reports\Usage\SummaryForMonths\UsageSummaryForMonth;
use Seatsio\SeatsioJsonMapper;

class UsageReports
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function summaryForAllMonths(): UsageSummaryForAllMonths
    {
        $res = $this->client->get('/reports/usage?version=2');
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new UsageSummaryForAllMonths());
    }

    /**
     * @return UsageDetails[]
     */
    public function detailsForMonth(Month $month): array
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize());
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json, array(), UsageDetails::class);
    }

    /**
     * @return UsageForObjectV1[] | UsageForObjectV2[]
     */
    public function detailsForEventInMonth(int $eventId, Month $month): array
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize() . '/event/' . $eventId);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        if (count($json) == 0 || !isset($json[0]->usageByReason)) {
            return $mapper->mapArray($json, array(), UsageForObjectV1::class);
        }
        return $mapper->mapArray($json, array(), UsageForObjectV2::class);
    }

}
