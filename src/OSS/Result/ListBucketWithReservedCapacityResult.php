<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ListBucketWithReservedCapacity;

class ListBucketWithReservedCapacityResult extends Result
{
    /**
     * @return ListBucketWithReservedCapacity
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $list = new ListBucketWithReservedCapacity();
        $list->parseFromXml($content);
        return $list;
    }
}