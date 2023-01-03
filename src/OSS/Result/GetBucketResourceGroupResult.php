<?php
namespace OSS\Result;
use OSS\Model\ResourceGroupConfig;

/**
 * Class GetBucketResourceGroupResult
 * @package OSS\Result
 */
class GetBucketResourceGroupResult extends Result
{
    /**
     * Parse the Resource Group Id from the response
     * @return string
     * @throws \OSS\Core\OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new ResourceGroupConfig();
        $config->parseFromXml($content);
        return $config->getResourceGroupId();
    }
}