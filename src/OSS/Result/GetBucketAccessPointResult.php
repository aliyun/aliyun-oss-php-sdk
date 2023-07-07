<?php

namespace OSS\Result;

use OSS\Model\AccessPointInfo;


/**
 * Class GetBucketAccessPointResult
 * @package OSS\Result
 */
class GetBucketAccessPointResult extends Result
{
    /**
     * @return AccessPointInfo
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $info = new AccessPointInfo();
        $info->parseFromXml($content);
        return $info;
    }
}