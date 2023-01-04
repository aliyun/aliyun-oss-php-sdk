<?php
namespace OSS\Result;
use OSS\Model\ListStyleConfig;
use OSS\Model\StyleConfig;

/**
 * Class ListBucketStyleResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/469893.html
 */
class ListBucketStyleResult extends Result
{
    /**
     * Parse the bucket style from the response
     * @return ListStyleConfig
     * @throws \OSS\Core\OssException
     */
    protected function parseDataFromResponse()
    {
        $xml = $this->rawResponse->body;
        $config = new ListStyleConfig();
        $config->parseFromXml($xml);
        return $config;
    }
}