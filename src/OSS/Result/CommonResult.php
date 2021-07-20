<?php

namespace OSS\Result;


/**
 * Class CommonResult
 * @package OSS\Result
 */
class CommonResult extends Result
{

    /**
     * @return array
     */
    protected function parseDataFromResponse()
    {
        return array_merge($this->rawResponse->header, $this->rawResponse->body);
    }
}
