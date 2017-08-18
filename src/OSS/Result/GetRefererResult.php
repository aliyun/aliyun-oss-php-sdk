<?php

namespace OSS\Result;


use OSS\Model\RefererConfig;

/**
 * Class GetRefererResult
 * @package OSS\Result
 */
class GetRefererResult extends Result
{
    /**
     * Parse RefererConfig object from the response
     *
     * @return RefererConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new RefererConfig();
        $config->parseFromXml($content);
        return $config;
    }

    /**
     * Checks if the response is OK according to its http status code.
     * [200-299]:OK, and the Referer config could be got; [404]: the referer config could not be found.
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