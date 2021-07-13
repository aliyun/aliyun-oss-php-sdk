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
        return $content = $this->rawResponse->body;

    }
}
