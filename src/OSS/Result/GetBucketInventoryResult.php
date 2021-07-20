<?php

namespace OSS\Result;
use OSS\Model\InventoryConfig;

/**
 * Class GetBucketInventoryResult
 * @package OSS\Result
 */
class GetBucketInventoryResult extends Result
{
    /**
     * @return string
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new InventoryConfig();
        $config->parseFromXml($content);
        return $config->getConfigs();
    }
}
