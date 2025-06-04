<?php

namespace Seatsio\TicketBuyers;

use GuzzleHttp\Client;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class TicketBuyers
{

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function add(array $uuids): AddTicketBuyerIdsResponse
    {
        $filtered = array_values(array_unique(array_filter($uuids, fn($id) => $id !== null)));

        $request = new stdClass();
        $request->ids = $filtered;

        $res = $this->client->post('/ticket-buyers', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new AddTicketBuyerIdsResponse());
    }

    public function listAll(): TicketBuyerIdPagedIterator
    {
        return $this->iterator()->all();
    }

    private function iterator(): TicketBuyerIdLister
    {
        return new TicketBuyerIdLister(new PageFetcher('/ticket-buyers', $this->client, function () {
            return new TicketBuyerIdPage();
        }));
    }
}
