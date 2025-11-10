<?php

namespace Seatsio\Seasons;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\GuzzleResponseDecoder;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Seasons
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var \Seatsio\SeatsioClient
     */
    private $seatsioClient;

    public function __construct(Client $client, \Seatsio\SeatsioClient $seatsioClient)
    {
        $this->client = $client;
        $this->seatsioClient = $seatsioClient;
    }

    public function create(string $chartKey, SeasonCreationParams $seasonCreationParams = null): Season
    {
        $request = new stdClass();
        $request->chartKey = $chartKey;

        if ($seasonCreationParams !== null) {
            if ($seasonCreationParams->key !== null) {
                $request->key = $seasonCreationParams->key;
            }

            if ($seasonCreationParams->name !== null) {
                $request->name = $seasonCreationParams->name;
            }

            if ($seasonCreationParams->eventKeys !== null) {
                $request->eventKeys = $seasonCreationParams->eventKeys;
            }

            if ($seasonCreationParams->numberOfEvents !== null) {
                $request->numberOfEvents = $seasonCreationParams->numberOfEvents;
            }

            if ($seasonCreationParams->tableBookingConfig !== null) {
                $request->tableBookingConfig = $this->serializeTableBookingConfig($seasonCreationParams->tableBookingConfig);
            }

            if ($seasonCreationParams->channels !== null) {
                $request->channels = $seasonCreationParams->channels;
            }

            if ($seasonCreationParams->forSaleConfig !== null) {
                $request->forSaleConfig = $seasonCreationParams->forSaleConfig;
            }

            if ($seasonCreationParams->forSalePropagated !== null) {
                $request->forSalePropagated = $seasonCreationParams->forSalePropagated;
            }

            if ($seasonCreationParams->objectCategories !== null) {
                $request->objectCategories = $seasonCreationParams->objectCategories;
            }

            if ($seasonCreationParams->categories !== null) {
                $request->categories = $seasonCreationParams->categories;
            }
        }

        $res = $this->client->post('/seasons', ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function update(string $key, UpdateSeasonParams $params): void
    {
        $request = new stdClass();

        if ($params->eventKey !== null) {
            $request->eventKey = $params->eventKey;
        }

        if ($params->tableBookingConfig !== null) {
            $request->tableBookingConfig = $this->serializeTableBookingConfig($params->tableBookingConfig);
        }

        if ($params->objectCategories !== null) {
            $request->objectCategories = $params->objectCategories;
        }

        if ($params->categories !== null) {
            $request->categories = $params->categories;
        }

        if ($params->name !== null) {
            $request->name = $params->name;
        }

        if ($params->forSalePropagated !== null) {
            $request->forSalePropagated = $params->forSalePropagated;
        }

        $this->client->post(UriTemplate::expand('/events/{key}', array("key" => $key)), ['json' => $request]);
    }

    public function retrieve(string $key): Season
    {
        return $this->seatsioClient->events->retrieve($key);
    }

    /**
     * @param $eventKeys string[]|null
     */
    public function createPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey = null, string $name = null, array $eventKeys = null): Season
    {
        $request = new stdClass();

        if ($partialSeasonKey !== null) {
            $request->key = $partialSeasonKey;
        }

        if ($name !== null) {
            $request->name = $name;
        }

        if ($eventKeys !== null) {
            $request->eventKeys = $eventKeys;
        }

        $res = $this->client->post(UriTemplate::expand('/seasons/{seasonKey}/partial-seasons', ['seasonKey' => $topLevelSeasonKey]), ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function addEventsToPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, array $eventKeys): Season
    {
        $request = new stdClass();
        $request->eventKeys = $eventKeys;
        $res = $this->client->post(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/actions/add-events', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey]), ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    /**
     * @param $eventKeys string[]|null
     * @return \Seatsio\Events\Event[]
     */
    public function createEvents(string $seasonKey, array $eventKeys = null, int $numberOfEvents = null): array
    {
        $request = new stdClass();

        if ($eventKeys !== null) {
            $request->eventKeys = $eventKeys;
        }

        if ($numberOfEvents !== null) {
            $request->numberOfEvents = $numberOfEvents;
        }

        $res = $this->client->post(UriTemplate::expand('/seasons/{seasonKey}/actions/create-events', ['seasonKey' => $seasonKey]), ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToObject($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->events, array(), 'Seatsio\Events\Event');
    }

    public function removeEventFromPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, string $eventKey): Season
    {
        $request = new stdClass();
        $res = $this->client->delete(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/events/{eventKey}', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey, 'eventKey' => $eventKey]), ['json' => $request]);
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    private function serializeTableBookingConfig($tableBookingConfig)
    {
        $request = new stdClass();
        $request->mode = $tableBookingConfig->mode;
        if ($tableBookingConfig->tables !== null) {
            $request->tables = $tableBookingConfig->tables;
        }
        return $request;
    }
}
