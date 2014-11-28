<?php
namespace BitbucketEventNotification\JsonParser;

/**
 * This class is json parser utility.
 *
 * @package BitbucketEventNotification\JsonParser
 */
class JsonParser
{
    /**
     * Decode a JSON string and return the array.
     *
     * @param string $jsonStr json string
     * @return array|false returns the json decoded array. false is returned if the json cannot be decoded.
     */
    public static function parse($jsonStr)
    {
        if (!is_string($jsonStr)) {
            return false;
        }

        $jsonArray = json_decode($jsonStr, true);
        if ($jsonArray === null) {
            return false;
        }

        return $jsonArray;
    }
}