<?php

namespace OBS\Result;

use OBS\Core\ObsException;
use OBS\ObsClient;

/**
 *
 * @package OBS\Result
 */
class SymlinkResult extends Result
{
    /**
     * @return string
     * @throws ObsException
     */
    protected function parseDataFromResponse()
    {
        $this->rawResponse->header[ObsClient::OBS_SYMLINK_TARGET] = rawurldecode($this->rawResponse->header[ObsClient::OBS_SYMLINK_TARGET]);
        return $this->rawResponse->header;
    }
}

