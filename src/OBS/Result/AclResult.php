<?php

namespace OBS\Result;

use OBS\Core\ObsException;

/**
 * The type of the return value of getBucketAcl, it wraps the data parsed from xml.
 *
 * @package OBS\Result
 */
class AclResult extends Result
{
    /**
     * @return string
     * @throws ObsException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new ObsException("body is null");
        }
        $xml = simplexml_load_string($content);
        if (isset($xml->AccessControlList->Grant)) {
            return strval($xml->AccessControlList->Grant);
        } else {
            throw new ObsException("xml format exception");
        }
    }
}