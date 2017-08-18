<?php

namespace OSS\Result;

use OSS\Core\OssException;

/**
 * Class UploadPartResult
 * @package OSS\Result
 */
class UploadPartResult extends Result
{
    /**
     * Gets the part ETag from the response.
     *
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $header = $this->rawResponse->header;
        if (isset($header["etag"])) {
            return $header["etag"];
        }
        throw new OssException("cannot get ETag");

    }
}