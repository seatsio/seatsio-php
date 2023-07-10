<?php

namespace Seatsio\Seasons;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use GuzzleHttp\Utils;
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

            if ($seasonCreationParams->eventKeys !== null) {
                $request->eventKeys = $seasonCreationParams->eventKeys;
            }

            if ($seasonCreationParams->numberOfEvents !== null) {
                $request->numberOfEvents = $seasonCreationParams->numberOfEvents;
            }

            if ($seasonCreationParams->tableBookingConfig !== null) {
                $request->tableBookingConfig = $this->serializeTableBookingConfig($seasonCreationParams->tableBookingConfig);
            }
        }

        $res = $this->client->post('/seasons', ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function retrieve(string $key): Season
    {
        return $this->seatsioClient->events->retrieve($key);
    }

    /**
     * @param $eventKeys string[]|null
     */
    public function createPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey = null, array $eventKeys = null): Season
    {
        $request = new stdClass();

        if ($partialSeasonKey !== null) {
            $request->key = $partialSeasonKey;
        }

        if ($eventKeys !== null) {
            $request->eventKeys = $eventKeys;
        }

        $res = $this->client->post(UriTemplate::expand('/seasons/{seasonKey}/partial-seasons', ['seasonKey' => $topLevelSeasonKey]), ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function addEventsToPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, array $eventKeys): Season
    {
        $request = new stdClass();
        $request->eventKeys = $eventKeys;
        $res = $this->client->post(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/actions/add-events', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey]), ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
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
        $json = Utils::jsonDecode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->mapArray($json->events, array(), 'Seatsio\Events\Event');
    }

    public function removeEventFromPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, string $eventKey): Season
    {
        $request = new stdClass();
        $res = $this->client->delete(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/events/{eventKey}', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey, 'eventKey' => $eventKey]), ['json' => $request]);
        $json = Utils::jsonDecode($res->getBody());
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
