<?php

namespace Seatsio\Seasons;

use GuzzleHttp\Client;
use GuzzleHttp\UriTemplate\UriTemplate;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;
use stdClass;

class Seasons
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
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

            if ($seasonCreationParams->socialDistancingRulesetKey !== null) {
                $request->socialDistancingRulesetKey = $seasonCreationParams->socialDistancingRulesetKey;
            }
        }

        $res = $this->client->post('/seasons', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
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
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function retrieve(string $seasonKey): Season
    {
        $res = $this->client->get(UriTemplate::expand('/seasons/{seasonKey}', ['seasonKey' => $seasonKey]));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function retrievePartialSeason(string $topLevelSeasonKey, string $partialSeasonKey): Season
    {
        $res = $this->client->get(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}', array("topLevelSeasonKey" => $topLevelSeasonKey, "partialSeasonKey" => $partialSeasonKey)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function delete(string $seasonKey): void
    {
        $this->client->delete(UriTemplate::expand('/seasons/{seasonKey}', array("seasonKey" => $seasonKey)));
    }

    public function deletePartialSeason(string $topLevelSeasonKey, string $partialSeasonKey): void
    {
        $this->client->delete(UriTemplate::expand('/seasons/{seasonKey}/partial-seasons/{partialSeason}', array("topLevelSeasonKey" => $topLevelSeasonKey, "partialSeasonKey" => $partialSeasonKey)));
    }

    public function addEventsToPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, array $eventKeys): Season
    {
        $request = new stdClass();
        $request->eventKeys = $eventKeys;
        $res = $this->client->post(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/actions/add-events', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey]), ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    /**
     * @param $eventKeys string[]|null
     */
    public function createEvents(string $seasonKey, array $eventKeys = null, int $numberOfEvents = null): Season
    {
        $request = new stdClass();

        if ($eventKeys !== null) {
            $request->eventKeys = $eventKeys;
        }

        if ($numberOfEvents !== null) {
            $request->numberOfEvents = $numberOfEvents;
        }

        $res = $this->client->post(UriTemplate::expand('/seasons/{seasonKey}/actions/create-events', ['seasonKey' => $seasonKey]), ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    public function removeEventFromPartialSeason(string $topLevelSeasonKey, string $partialSeasonKey, string $eventKey): Season
    {
        $request = new stdClass();
        $res = $this->client->delete(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/events/{eventKey}', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey, 'eventKey' => $eventKey]), ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
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

    public function listAll(): SeasonPagedIterator
    {
        return $this->iterator()->all();
    }

    public function listFirstPage(int $pageSize = null): SeasonPage
    {
        return $this->iterator()->firstPage($pageSize);
    }

    public function listPageAfter(int $afterId, int $pageSize = null): SeasonPage
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    public function listPageBefore(int $beforeId, int $pageSize = null): SeasonPage
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    private function iterator(): SeasonLister
    {
        return new SeasonLister(new PageFetcher('/seasons', $this->client, function () {
            return new SeasonPage();
        }));
    }
}
