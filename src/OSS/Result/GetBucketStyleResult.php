<?php
namespace OSS\Result;
use OSS\Model\StyleConfig;

/**
 * Class GetBucketStyleResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/469893.html
 */
class GetBucketStyleResult extends Result
{
    /**
     * Parse the bucket style from the response
     * @return StyleConfig
     * @throws \OSS\Core\OssException
     */
    protected function parseDataFromResponse()
    {
        $xml = $this->rawResponse->body;
        $config = new StyleConfig();
        $config->parseFromXml($xml);
        return $config;
    }
}