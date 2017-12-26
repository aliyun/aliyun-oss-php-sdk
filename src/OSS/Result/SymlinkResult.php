<?php

namespace OSS\Result;

use OSS\Core\OssException;

/**
 *
 * @package OSS\Result
 */
class SymlinkResult extends Result
{
    /**
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $this->rawResponse->header[self::OSS_SYMLINK_TARGET] = rawurldecode($this->rawResponse->header[self::OSS_SYMLINK_TARGET]);

        return $this->rawResponse->header;
    }
    const OSS_SYMLINK_TARGET = 'x-oss-symlink-target';
}