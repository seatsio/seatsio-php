<?php

namespace Seatsio;

use GuzzleHttp\Psr7\StreamWrapper;
use GuzzleHttp\Utils;
use JsonMachine\Items;
use JsonMapper;

class GuzzleResponseDecoder
{

    public static function decodeToJson($res, $pointer = null)
    {
        $options = [];
        if ($pointer != null) {
            $options["pointer"] = $pointer;
        }

        return Items::fromStream(StreamWrapper::getResource($res->getBody()), $options);
    }

    public static function decodeToObject($res)
    {
        $mapper = self::mapperThatSetsUnknownProperties();
        $json = GuzzleResponseDecoder::decodeToJson($res);
        return $mapper->map($json, 'stdClass');
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

    /**
     * @return JsonMapper
     */
    public static function mapperThatSetsUnknownProperties(): JsonMapper
    {
        $mapper = new JsonMapper();
        $mapper->undefinedPropertyHandler = function ($object, $propName, $jsonValue) {
            $object->{$propName} = $jsonValue;
        };
        return $mapper;
    }
}
