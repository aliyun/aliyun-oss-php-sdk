<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\WebsiteConfig;

/**
 * Class GetWebsiteResult
 * @package OSS\Result
 */
class GetWebsiteResult extends Result
{
    /**
     * @return WebsiteConfig
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }

        $websiteConfig = new WebsiteConfig();
        $websiteConfig->parseFromXml($content);
        return $websiteConfig;
    }

    /**
     * Judged according to the return HTTP status code, [200-299] that is OK, get the bucket configuration interface,
     * 404 is also considered a valid response
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