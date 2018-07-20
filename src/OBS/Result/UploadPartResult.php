<?php

namespace OBS\Result;

use OBS\Core\ObsException;

/**
 * Class UploadPartResult
 * @package OBS\Result
 */
class UploadPartResult extends Result
{
    /**
     * 结果中part的ETag
     *
     * @return string
     * @throws ObsException
     */
    protected function parseDataFromResponse()
    {
        $header = $this->rawResponse->header;
        if (isset($header["etag"])) {
            return $header["etag"];
        }
        throw new ObsException("cannot get ETag");

    }
}