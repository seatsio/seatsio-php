<?php

namespace Seatsio;

class Region
{
    /** @var string */
    private $url;

    public static function US()
    {
        return new Region(self::urlForId("us"));
    }

    public static function EU()
    {
        return new Region(self::urlForId("eu"));
    }

    /**
     * @return Region
     */
    public static function withUrl($url)
    {
        return new Region($url);
    }

    private function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    private static function urlForId($id)
    {
        return str_replace("{region}", $id, "https://api-{region}.seatsio.net/");
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->url;
    }
}
