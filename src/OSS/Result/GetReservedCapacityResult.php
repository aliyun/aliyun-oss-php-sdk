<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ReservedCapacityRecord;

class GetReservedCapacityResult extends Result
{
    /**
     * @return ReservedCapacityRecord
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $record = new ReservedCapacityRecord();
        $record->parseFromXml($content);
        return $record;
    }
}