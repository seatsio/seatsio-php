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

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $chartKey string
     * @param $seasonCreationParams SeasonCreationParams
     * @return Season
     */
    public function create($chartKey, $seasonCreationParams = null)
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
     * @param $topLevelSeasonKey string
     * @param $partialSeasonKey string
     * @param $eventKeys string[]
     * @return Season
     */
    public function createPartialSeason($topLevelSeasonKey, $partialSeasonKey = null, $eventKeys = null)
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

    /**
     * @param $seasonKey string
     * @return Season
     */
    public function retrieve($seasonKey)
    {
        $res = $this->client->get(UriTemplate::expand('/seasons/{seasonKey}', ['seasonKey' => $seasonKey]));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    /**
     * @param $topLevelSeasonKey string
     * @param $partialSeasonKey string
     * @return Season
     */
    public function retrievePartialSeason($topLevelSeasonKey, $partialSeasonKey)
    {
        $res = $this->client->get(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}', array("topLevelSeasonKey" => $topLevelSeasonKey, "partialSeasonKey" => $partialSeasonKey)));
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    /**
     * @param $seasonKey string
     * @return void
     */
    public function delete($seasonKey)
    {
        $this->client->delete(UriTemplate::expand('/seasons/{seasonKey}', array("seasonKey" => $seasonKey)));
    }

    /**
     * @param $topLevelSeasonKey string
     * @param $partialSeasonKey string
     * @return void
     */
    public function deletePartialSeason($topLevelSeasonKey, $partialSeasonKey)
    {
        $this->client->delete(UriTemplate::expand('/seasons/{seasonKey}/partial-seasons/{partialSeason}', array("topLevelSeasonKey" => $topLevelSeasonKey, "partialSeasonKey" => $partialSeasonKey)));
    }

    /**
     * @param $topLevelSeasonKey string
     * @param $partialSeasonKey string
     * @param $eventKeys string[]
     * @return Season
     */
    public function addEventsToPartialSeason($topLevelSeasonKey, $partialSeasonKey, $eventKeys)
    {
        $request = new stdClass();
        $request->eventKeys = $eventKeys;
        $res = $this->client->post(UriTemplate::expand('/seasons/{topLevelSeasonKey}/partial-seasons/{partialSeasonKey}/actions/add-events', ['topLevelSeasonKey' => $topLevelSeasonKey, 'partialSeasonKey' => $partialSeasonKey]), ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Season());
    }

    /**
     * @param $seasonKey string
     * @param $eventKeys string[]
     * @param null $numberOfEvents int
     * @return Season
     */
    public function createEvents($seasonKey, $eventKeys = null, $numberOfEvents = null)
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

    /**
     * @param $topLevelSeasonKey string
     * @param $partialSeasonKey string
     * @param $eventKey string
     * @return Season
     */
    public function removeEventFromPartialSeason($topLevelSeasonKey, $partialSeasonKey, $eventKey)
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

    /**
     * @return SeasonPagedIterator
     */
    public function listAll()
    {
        return $this->iterator()->all();
    }

    /**
     * @param $pageSize int
     * @return SeasonPage
     */
    public function listFirstPage($pageSize = null)
    {
        return $this->iterator()->firstPage($pageSize);
    }

    /**
     * @param $afterId int
     * @param $pageSize int
     * @return SeasonPage
     */
    public function listPageAfter($afterId, $pageSize = null)
    {
        return $this->iterator()->pageAfter($afterId, $pageSize);
    }

    /**
     * @param $beforeId int
     * @param $pageSize int
     * @return SeasonPage
     */
    public function listPageBefore($beforeId, $pageSize = null)
    {
        return $this->iterator()->pageBefore($beforeId, $pageSize);
    }

    /**
     * @return SeasonLister
     */
    private function iterator()
    {
        return new SeasonLister(new PageFetcher('/seasons', $this->client, function () {
            return new SeasonPage();
        }));
    }
}
