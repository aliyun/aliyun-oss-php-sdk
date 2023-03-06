<?php

namespace OSS\Result;
use OSS\Core\OssException;
use OSS\Model\InventoryConfig;

/**
 * Class GetBucketInventoryResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/159739.html
 */
class GetBucketInventoryResult extends Result
{
    /**
     * @return InventoryConfig
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $config = new InventoryConfig();
        $config->parseFromXml($content);
        return $config;

    }
}
