<?php

namespace OSS\Result;

use OSS\Model\ListAccessPoints;

/**
 * Class ListBucketAccessPointResult
 * @package OSS\Result
 */
class ListBucketAccessPointResult extends Result
{
    /**
     * @return ListAccessPoints
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $info = new ListAccessPoints();
        $info->parseFromXml($content);
        return $info;
    }
}