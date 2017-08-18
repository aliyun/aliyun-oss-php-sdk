<?php

namespace OSS\Result;


/**
 * Class HeaderResult
 * @package OSS\Result
 * @link https://docs.aliyun.com/?spm=5176.383663.13.7.HgUIqL#/pub/oss/api-reference/object&GetObjectMeta
 */
class HeaderResult extends Result
{
    /**
     * Returns the header of ResponseCore instance.
     *
     * @return array
     */
    protected function parseDataFromResponse()
    {
        return empty($this->rawResponse->header) ? array() : $this->rawResponse->header;
    }

}