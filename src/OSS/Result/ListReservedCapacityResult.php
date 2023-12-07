<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ListReservedCapacity;

class ListReservedCapacityResult extends Result
{
    /**
     * @return ListReservedCapacity
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $list = new ListReservedCapacity();
        $list->parseFromXml($content);
        return $list;
    }
}