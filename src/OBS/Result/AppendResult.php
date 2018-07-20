<?php

namespace OBS\Result;

use OBS\Core\ObsException;

/**
 * Class AppendResult
 * @package OBS\Result
 */
class AppendResult extends Result
{
    /**
     * Get the value of next-append-position from append's response headers
     *
     * @return int
     * @throws ObsException
     */
    protected function parseDataFromResponse()
    {
        $header = $this->rawResponse->header;
        if (isset($header["x-obs-next-append-position"])) {
            return intval($header["x-obs-next-append-position"]);
        }
        throw new ObsException("cannot get next-append-position");
    }
}