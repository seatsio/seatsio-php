<?php

namespace Seatsio\Reports\Usage;

use GuzzleHttp\Client;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV1;
use Seatsio\Reports\Usage\DetailsForEventInMonth\UsageForObjectV2;
use Seatsio\Reports\Usage\DetailsForMonth\UsageDetails;
use Seatsio\Reports\Usage\SummaryForMonths\Month;
use Seatsio\Reports\Usage\SummaryForMonths\UsageSummaryForAllMonths;
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
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new UsageSummaryForAllMonths());
    }

    /**
     * @return UsageDetails[]
     */
    public function detailsForMonth(Month $month): array
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize());
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json, array(), UsageDetails::class);
    }

    /**
     * @return UsageForObjectV1[] | UsageForObjectV2[]
     */
    public function detailsForEventInMonth(int $eventId, Month $month): array
    {
        $res = $this->client->get('/reports/usage/month/' . $month->serialize() . '/event/' . $eventId);
        $mapper = SeatsioJsonMapper::create();
        $json = GuzzleResponseDecoder::decodeToJson($res);

        $report = [];
        foreach ($json as $item) {
            $targetClass = isset($item->usageByReason) ? UsageForObjectV2::class : UsageForObjectV1::class;
            $report[] = $mapper->map($item, $targetClass);
        }
        return $report;
    }

}
