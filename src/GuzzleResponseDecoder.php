<?php

namespace Seatsio;

use GuzzleHttp\Psr7\StreamWrapper;
use JsonMachine\Items;

class GuzzleResponseDecoder
{

    public static function decodeToJson($res)
    {
        return Items::fromStream(StreamWrapper::getResource($res->getBody()));
    }

    public static function decodeToObject($res)
    {
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $object = new \stdClass();
        foreach ($json as $name => $item) {
            $object->$name = $item;
        }
        return $object;
    }

    public static function decodeToArray($res)
    {
        $json = GuzzleResponseDecoder::decodeToJson($res);
        $array = [];
        foreach ($json as $name => $item) {
            $array[$name] = json_decode(json_encode($item), true);
        }
        return $array;
    }
}
