<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ReplicationLocation;

/**
 * Class GetBucketReplicationLocationResult interface returns the result class, encapsulated
 * The returned xml data is parsed
 *
 * @package OSS\Result
 */
class GetBucketReplicationLocationResult extends Result
{
    /**
     * Parse data from response
     * @return ReplicationLocation
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $replicationLocation= new ReplicationLocation();
        $replicationLocation->parseFromXml($content);
        return $replicationLocation;

    }
}