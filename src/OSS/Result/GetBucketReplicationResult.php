<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\BucketReplicationInfo;
use OSS\Model\ReplicationConfig;

/**
 * Class GetBucketReplicationResult interface returns the result class, encapsulated
 * The returned xml data is parsed
 *
 * @package OSS\Result
 */
class GetBucketReplicationResult extends Result
{
    /**
     * @return ReplicationConfig
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }

        $replicationConfig = new ReplicationConfig();
        $replicationConfig->parseFromXml($content);
        return $replicationConfig;
    }

}