<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\AppendInfo;

/**
 * Class AppendResult
 * @package OSS\Result
 */
class AppendResult extends Result
{
    /**
     * @return AppendInfo
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $header = $this->rawResponse->header;
        if (isset($header["x-oss-next-append-position"])) {
            return new AppendInfo($header["x-oss-next-append-position"],$header["x-oss-hash-crc64ecma"]);
        }
        throw new OssException("cannot get next-append-position");
    }
}