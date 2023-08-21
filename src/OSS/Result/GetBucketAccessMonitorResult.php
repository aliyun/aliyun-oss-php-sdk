<?php

namespace OSS\Result;
use OSS\Model\AccessMonitorConfig;

/**
 * Class GetBucketAccessMonitorResult
 * @package OSS\Result
 */
class GetBucketAccessMonitorResult extends Result
{
    /**
     * @return string
     * @throws \OSS\Core\OssException
     */
	protected function parseDataFromResponse()
	{
		$content = $this->rawResponse->body;
		$config = new AccessMonitorConfig();
		$config->parseFromXml($content);
		return $config->getStatus();
	}
}
