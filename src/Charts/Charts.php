<?php

namespace Seatsio\Charts;

use Psr\Http\Message\StreamInterface;
use Seatsio\Events\EventLister;
use Seatsio\Events\EventPage;
use Seatsio\PageFetcher;
use Seatsio\SeatsioJsonMapper;

class Charts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $name string
     * @param $venueType string
     * @param $categories array|\Seatsio\Charts\Category[]
     * @return Chart
     */
    public function create($name = null, $venueType = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($venueType !== null) {
            $request->venueType = $venueType;
        }
        if ($categories != null) {
            $request->categories = $categories;
        }
        $res = $this->client->post('/charts', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @param $key string
     * @param $name string
     * @param $categories array|\Seatsio\Charts\Category[]
     * @return void
     */
    public function update($key, $name = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name !== null) {
            $request->name = $name;
        }
        if ($categories !== null) {
            $request->categories = $categories;
        }
        $this->client->post('/charts/' . $key, ['json' => $request]);
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function retrieve($key)
    {
        $res = $this->client->get('/charts/' . $key);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, new Chart());
    }

    /**
     * @param $key string
     * @return object
     */
    public function retrievePublishedVersion($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return object
     */
    public function retrieveDraftVersion($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return void
     */
    public function publishDraftVersion($key)
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/publish');
    }

    /**
     * @param $key string
     * @return void
     */
    public function discardDraftVersion($key)
    {
        $this->client->post('/charts/' . $key . '/version/draft/actions/discard');
    }

    /**
     * @param $key string
     * @return void
     */
    public function moveToArchive($key)
    {
        $this->client->post('/charts/' . $key . '/actions/move-to-archive');
    }

    /**
     * @param $key string
     * @return void
     */
    public function moveOutOfArchive($key)
    {
        $this->client->post('/charts/' . $key . '/actions/move-out-of-archive');
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function copy($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function copyDraftVersion($key)
    {
        $res = $this->client->post('/charts/' . $key . '/version/draft/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return Chart
     */
    public function copyToSubaccount($key, $subaccountId)
    {
        $res = $this->client->post('/charts/' . $key . '/version/published/actions/copy-to/' . $subaccountId);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @param $key string
     * @return StreamInterface
     */
    public function retrievePublishedVersionThumbnail($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/published/thumbnail');
        return $res->getBody();
    }

    /**
     * @param $key string
     * @return StreamInterface
     */
    public function retrieveDraftVersionThumbnail($key)
    {
        $res = $this->client->get('/charts/' . $key . '/version/draft/thumbnail');
        return $res->getBody();
    }

    /**
     * @return string[]
     */
    public function listAllTags()
    {
        $res = $this->client->get('/charts/tags');
        return \GuzzleHttp\json_decode($res->getBody())->tags;
    }

    /**
     * @param $key string
     * @param $tag string
     * @return void
     */
    public function addTag($key, $tag)
    {
        $this->client->post(\GuzzleHttp\uri_template('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    /**
     * @param $key string
     * @param $tag string
     * @return void
     */
    public function removeTag($key, $tag)
    {
        $this->client->delete(\GuzzleHttp\uri_template('/charts/{key}/tags/{tag}', array("key" => $key, "tag" => $tag)));
    }

    /**
     * @return ChartLister
     */
    public function iterator()
    {
        return new ChartLister(new PageFetcher('/charts', $this->client, function () {
            return new ChartPage();
        }));
    }

    /**
     * @return ChartLister
     */
    public function archive()
    {
        return new ChartLister(new PageFetcher('/charts/archive', $this->client, function () {
            return new ChartPage();
        }));
    }

    /**
     * @param $key string
     * @return EventLister
     */
    public function events($key)
    {
        return new EventLister(new PageFetcher('/charts/' . $key . '/events', $this->client, function () {
            return new EventPage();
        }));
    }

}