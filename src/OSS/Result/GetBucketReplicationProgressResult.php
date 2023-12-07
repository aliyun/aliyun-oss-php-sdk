<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ReplicationProgress;

/**
 * Class GetBucketReplicationProgressResult interface returns the result class, encapsulated
 * The returned xml data is parsed
 *
 * @package OSS\Result
 */
class GetBucketReplicationProgressResult extends Result
{
    /**
     * Parse data from response
     * 
     * @return ReplicationProgress
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }

        $replicationConfig = new ReplicationProgress();
        $replicationConfig->parseFromXml($content);
        return $replicationConfig;
    }
}