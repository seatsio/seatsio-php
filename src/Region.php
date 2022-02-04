<?php

namespace Seatsio;

class Region
{
    /** @var string */
    private $url;

    public static function EU(): Region
    {
        return new Region(self::urlForId("eu"));
    }

    public static function NA(): Region
    {
        return new Region(self::urlForId("na"));
    }

    public static function SA(): Region
    {
        return new Region(self::urlForId("sa"));
    }

    public static function OC(): Region
    {
        return new Region(self::urlForId("oc"));
    }

    public static function withUrl($url): Region
    {
        return new Region($url);
    }

    private function __construct($url)
    {
        $this->url = $url;
    }

    private static function urlForId(string $id): string
    {
        return str_replace("{region}", $id, "https://api-{region}.seatsio.net/");
    }

    public function url(): string
    {
        return $this->url;
    }
}
