<?php
namespace OSS\Result;

use OSS\Core\OssException;

/**
 * Class GetLocationResult getBucketLocation interface returns the result class, encapsulated
 * The returned xml data is parsed
 *
 * @package OSS\Result
 */
class GetLocationResult extends Result
{

    /**
     * Parse data from response
     * @return false|\SimpleXMLElement|string|null
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (!isset($content) || $content === "") {
            throw new OssException("body is null");
        }
        return simplexml_load_string($content);
    }
}