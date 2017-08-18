<?php

namespace OSS\Model;

/**
 * Class BucketListInfo
 *
 * It's the type of return value of ListBuckets.
 *
 * @package OSS\Model
 */
class BucketListInfo
{
    /**
     * BucketListInfo constructor.
     * @param array $bucketList
     */
    public function __construct(array $bucketList)
    {
        $this->bucketList = $bucketList;
    }

    /**
     * Gets the BucketInfo list
     *
     * @return BucketInfo[]
     */
    public function getBucketList()
    {
        return $this->bucketList;
    }

    /**
     * BucketInfo array
     *
     * @var array
     */
    private $bucketList = array();
}