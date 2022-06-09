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


    /**
     * @param string $eventKey
     * @param string $channelKey
     * @param string $name
     * @param string $color
     * @param int|null $index
     * @param array|null $objects
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(string $eventKey, string $channelKey, string $name, string $color, int $index = null, array $objects = null)
    {
        $request = new stdClass();
        $request->key = $channelKey;
        $request->name = $name;
        $request->color = $color;
        if ($index !== null) {
            $request->index = $index;
        }
        if ($objects !== null) {
            $request->objects = $objects;
        }
        $this->client->post(UriTemplate::expand('/events/{key}/channels', array("key" => $eventKey)), ['json' => $request]);
    }

    public function remove(string $eventKey, string $channelKey)
    {
        $this->client->delete(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}', array("eventKey" => $eventKey, "channelKey" => $channelKey)));
    }

    public function update()
    {

    }

    /**
     * @param string $eventKey
     * @param string $channelKey
     * @param array $objects
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addObjects(string $eventKey, string $channelKey, array $objects)
    {
        $request = new stdClass();
        $request->objects = $objects;
        $this->client->post(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}/objects', array(
                "eventKey" => $eventKey,
                "channelKey" => $channelKey)
        ), ['json' => $request]);
    }

    public function removeObjects(string $eventKey, string $channelKey, array $objects)
    {
        $request = new stdClass();
        $request->objects = $objects;
        $this->client->delete(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}/objects', array(
                "eventKey" => $eventKey,
                "channelKey" => $channelKey)
        ), ['json' => $request]);
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
