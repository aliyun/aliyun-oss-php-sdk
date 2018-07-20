<?php
namespace OBS\Result;

use OBS\Core\ObsException;

/**
 * Class GetLocationResult getBucketLocation interface returns the result class, encapsulated
 * The returned xml data is parsed
 *
 * @package OBS\Result
 */
class GetLocationResult extends Result
{

    /**
     * Parse data from response
     * 
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
        return $xml;
    }
}