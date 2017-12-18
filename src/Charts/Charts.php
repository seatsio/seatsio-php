<?php

namespace Seatsio\Charts;

use JsonMapper;
use Psr\Http\Message\StreamInterface;
use Seatsio\Events\EventLister;
use Seatsio\Events\EventPage;
use Seatsio\PageFetcher;

class Charts
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    private $pageSize;

    public function __construct($client, $pageSize)
    {
        $this->client = $client;
        $this->pageSize = $pageSize;
    }

    /**
     * @return Chart
     */
    public function create($name = null, $venueType = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name) {
            $request->name = $name;
        }
        if ($venueType) {
            $request->venueType = $venueType;
        }
        if ($categories) {
            $request->categories = $categories;
        }
        $res = $this->client->request('POST', '/charts', ['json' => $request]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Chart());
    }

    /**
     * @return void
     */
    public function update($key, $name = null, $categories = null)
    {
        $request = new \stdClass();
        if ($name) {
            $request->name = $name;
        }
        if ($categories) {
            $request->categories = $categories;
        }
        $this->client->request('POST', '/charts/' . $key, ['json' => $request]);
    }

    /**
     * @return Chart
     */
    public function retrieve($key)
    {
        $res = $this->client->request('GET', '/charts/' . $key);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = new JsonMapper();
        return $mapper->map($json, new Chart());
    }

    /**
     * @return mixed
     */
    public function retrievePublishedChart($key)
    {
        $res = $this->client->request('GET', '/charts/' . $key . '/version/published');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return void
     */
    public function publishDraft($key)
    {
        $this->client->request('POST', '/charts/' . $key . '/version/draft/actions/publish');
    }

    /**
     * @return void
     */
    public function discardDraft($key)
    {
        $this->client->request('POST', '/charts/' . $key . '/version/draft/actions/discard');
    }

    /**
     * @return void
     */
    public function moveToArchive($key)
    {
        $this->client->request('POST', '/charts/archive/' . $key);
    }

    /**
     * @return void
     */
    public function moveOutOfArchive($key)
    {
        $this->client->request('DELETE', '/charts/archive/' . $key);
    }

    /**
     * @return Chart
     */
    public function copy($key)
    {
        $res = $this->client->request('POST', '/charts/' . $key . '/version/published/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return Chart
     */
    public function copyDraft($key)
    {
        $res = $this->client->request('POST', '/charts/' . $key . '/version/draft/actions/copy');
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return Chart
     */
    public function copyToSubaccount($key, $subaccountId)
    {
        $res = $this->client->request('POST', '/charts/' . $key . '/version/published/actions/copy-to/' . $subaccountId);
        return \GuzzleHttp\json_decode($res->getBody());
    }

    /**
     * @return StreamInterface
     */
    public function retrieveThumbnail($key)
    {
        $res = $this->client->request('GET', '/charts/' . $key . '/version/published/thumbnail');
        return $res->getBody();
    }

    /**
     * @return string[]
     */
    public function listAllTags()
    {
        $res = $this->client->request('GET', '/charts/tags');
        return \GuzzleHttp\json_decode($res->getBody())->tags;
    }

    /**
     * @return string[]
     */
    public function listTags($key)
    {
        $res = $this->client->request('GET', '/charts/' . $key . '/tags');
        return \GuzzleHttp\json_decode($res->getBody())->tags;
    }

    /**
     * @return void
     */
    public function addTag($key, $tag)
    {
        $this->client->request('POST', '/charts/' . $key . '/tags/' . $tag);
    }

    /**
     * @return void
     */
    public function removeTag($key, $tag)
    {
        $this->client->request('DELETE', '/charts/' . $key . '/tags/' . $tag);
    }

    /**
     * @return ChartLister
     */
    public function lister()
    {
        return new ChartLister(new PageFetcher('/charts', $this->client, $this->pageSize, function () {
            return new ChartPage();
        }));
    }

    /**
     * @return ChartLister
     */
    public function archive()
    {
        return new ChartLister(new PageFetcher('/charts/archive', $this->client, $this->pageSize, function () {
            return new ChartPage();
        }));
    }

    /**
     * @return EventLister
     */
    public function events($key)
    {
        return new EventLister(new PageFetcher('/charts/' . $key . '/events', $this->client, $this->pageSize, function () {
            return new EventPage();
        }));
    }

}