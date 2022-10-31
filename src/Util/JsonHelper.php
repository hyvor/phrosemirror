<?php

namespace Hyvor\Phrosemirror\Util;

use Hyvor\Phrosemirror\Exception\InvalidJsonException;

class JsonHelper
{

    /**
     * @return array<mixed>
     */
    public static function getJsonArray(mixed $json) : array
    {

        if (is_array($json))
            return $json;

        if (is_object($json))
            return (array) $json;

        if (is_string($json)) {
            $json = json_decode($json, true);

            if (!is_array($json))
                throw new InvalidJsonException('Unable to decode JSON');

            return $json;
        }

        throw new InvalidJsonException('JSON should be an array, object, or a string');

    }

}