<?php

namespace OSS\Result;

use OSS\Model\CnameList;

class GetBucketCnameResult extends Result
{
    /**
     * @return CnameList
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $list = new CnameList();
        $list->parseFromXml($content);
        return $list;
    }

}