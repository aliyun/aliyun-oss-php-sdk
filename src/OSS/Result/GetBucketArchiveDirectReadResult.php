<?php

namespace OSS\Result;
use OSS\Model\ArchiveDirectReadConfig;

/**
 * Class GetBucketArchiveDirectReadResult
 * @package OSS\Result
 */
class GetBucketArchiveDirectReadResult extends Result
{
    /**
     * @return bool
     */
	protected function parseDataFromResponse()
	{
		$content = $this->rawResponse->body;
		$config = new ArchiveDirectReadConfig();
		$config->parseFromXml($content);
		return $config->getEnabled();
	}
}
