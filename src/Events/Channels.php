<?php

namespace Seatsio\Events;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use stdClass;

class Channels
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function replace(string $eventKey, array $channels): void
    {
        $request = new stdClass();
        $request->channels = $channels;
        $this->client->post(UriTemplate::expand('/events/{key}/channels/update', array("key" => $eventKey)), ['json' => $request]);
    }

    public function setObjects(string $eventKey, array $channelConfig): void
    {
        $request = new stdClass();
        $request->channelConfig = $channelConfig;
        $this->client->post(UriTemplate::expand('/events/{key}/channels/assign-objects', array("key" => $eventKey)), ['json' => $request]);
    }
}
