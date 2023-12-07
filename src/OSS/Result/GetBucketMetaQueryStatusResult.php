<?php

namespace OSS\Result;
use OSS\Model\BucketMetaQueryStatus;
class GetBucketMetaQueryStatusResult extends Result
{
    /**
     * @return BucketMetaQueryStatus
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $query = new BucketMetaQueryStatus();
        $query->parseFromXml($content);
        return $query;
    }
}