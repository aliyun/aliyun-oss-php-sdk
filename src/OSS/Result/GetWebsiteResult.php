<?php

namespace OSS\Result;

use OSS\Model\WebsiteConfig;

/**
 * Class GetWebsiteResult
 * @package OSS\Result
 */
class GetWebsiteResult extends Result
{
    /**
     * Parse WebsiteConfig object from response
     *
     * @return WebsiteConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new WebsiteConfig();
        $config->parseFromXml($content);
        return $config;
    }

    /**
     * Checks if the response is OK according to its http status code.
     * [200-299]:OK, and the Website config could be got; [404]: the website config could not be found.
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