<?php

namespace OBS\Result;

use OBS\Core\ObsException;


/**
 * Class initiateMultipartUploadResult
 * @package OBS\Result
 */
class InitiateMultipartUploadResult extends Result
{
    /**
     * Get uploadId in result and return
     *
     * @throws ObsException
     * @return string
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $xml = simplexml_load_string($content);
        if (isset($xml->UploadId)) {
            return strval($xml->UploadId);
        }
        throw new ObsException("cannot get UploadId");
    }
}