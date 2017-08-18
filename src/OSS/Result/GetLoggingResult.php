<?php

namespace OSS\Result;

use OSS\Model\LoggingConfig;


/**
 * Class GetLoggingResult
 * @package OSS\Result
 */
class GetLoggingResult extends Result
{
    /**
     * Parse the LoggingConfig from response
     *
     * @return LoggingConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new LoggingConfig();
        $config->parseFromXml($content);
        return $config;
    }

    /**
     * Checks if the response is OK according to its http status code.
     * [200-299]:OK, and the Logging config could be got; [404]: the logging config could not be found.
     *
     * @return bool
     */
    protected function isResponseOk()
    {
        $status = $this->rawResponse->status;
        if ((int)(intval($status) / 100) == 2 || (int)(intval($status)) === 404) {
            return true;
        }
        return false;
    }
}