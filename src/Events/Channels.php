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
     * Add a channel
     *
     * @param string $eventKey
     * @param string $channelKey
     * @param string $name
     * @param string $color
     * @param int|null $index
     * @param array|null $objects
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(string $eventKey, string $channelKey, string $name, string $color, ?int $index = null, ?array $objects = null)
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

    public function addMultiple(string $eventKey, array $channelCreationParams)
    {
        $request = array();
        foreach ($channelCreationParams as $param) {
            $channelToCreate = new stdClass();
            if ($param->channelKey !== null) {
                $channelToCreate->key = $param->channelKey;
            }
            if ($param->name !== null) {
                $channelToCreate->name = $param->name;
            }
            if ($param->color !== null) {
                $channelToCreate->color = $param->color;
            }
            if ($param->index !== null) {
                $channelToCreate->index = $param->index;
            }
            if ($param->objects !== null) {
                $channelToCreate->objects = $param->objects;
            }
            $request[] = $channelToCreate;
        }
        $this->client->post(UriTemplate::expand('/events/{key}/channels', array("key" => $eventKey)), ['json' => $request]);
    }

    /**
     * remove a channel by channel key
     *
     * @param string $eventKey
     * @param string $channelKey
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function remove(string $eventKey, string $channelKey)
    {
        $this->client->delete(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}', array("eventKey" => $eventKey, "channelKey" => $channelKey)));
    }

    /**
     * update the name, color or objects of a channel
     *
     * @param string $eventKey
     * @param string $channelKey
     * @param string|null $name
     * @param string|null $color
     * @param array|null $objects
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $eventKey, string $channelKey, ?string $name = null, ?string $color = null, ?array $objects = null)
    {
        $request = new stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($color !== null) {
            $request->color = $color;
        }
        if ($objects !== null) {
            $request->objects = $objects;
        }
        $this->client->post(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}', array("eventKey" => $eventKey, "channelKey" => $channelKey)),
            ['json' => $request]);
    }

    /**
     * add objects to a channel.
     *
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

    /**
     * Remove objects from a channel
     *
     * @param string $eventKey
     * @param string $channelKey
     * @param array $objects
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function removeObjects(string $eventKey, string $channelKey, array $objects)
    {
        $request = new stdClass();
        $request->objects = $objects;
        $this->client->delete(UriTemplate::expand('/events/{eventKey}/channels/{channelKey}/objects', array(
                "eventKey" => $eventKey,
                "channelKey" => $channelKey)
        ), ['json' => $request]);
    }

    /**
     * Replace one channel completely with a new channel
     *
     * @param string $eventKey
     * @param array $channels
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function replace(string $eventKey, array $channels): void
    {
        $request = new stdClass();
        $request->channels = $channels;
        $this->client->post(UriTemplate::expand('/events/{key}/channels/replace', array("key" => $eventKey)), ['json' => $request]);
    }
}
