<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ListInventoryConfig;

/**
 * Class ListObjectsResult
 * @package OSS\Result
 */
class ListBucketInventoryResult extends Result
{
    /**
     * @return ListInventoryConfig
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $config = new ListInventoryConfig();
        $config->parseFromXml($content);
        return $config;
    }

}