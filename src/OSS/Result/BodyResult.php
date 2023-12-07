<?php

namespace OSS\Result;


/**
 * Class BodyResult
 * @package OSS\Result
 */
class BodyResult extends Result
{
    /**
     * @return string
     */
    protected function parseDataFromResponse()
    {
        return isset($this->rawResponse->body) && $this->rawResponse->body !== "" ? $this->rawResponse->body :"";
    }
}