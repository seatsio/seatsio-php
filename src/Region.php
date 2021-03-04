<?php

namespace Seatsio;

class Region
{
    /** @var string */
    private $url;

    public static function EU()
    {
        return new Region(self::urlForId("eu"));
    }

    public static function NA()
    {
        return new Region(self::urlForId("na"));
    }

    public static function SA()
    {
        return new Region(self::urlForId("sa"));
    }

    public static function OC()
    {
        return new Region(self::urlForId("oc"));
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
