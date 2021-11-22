<?php

namespace OSS\Model;

/**
 * Class ObjectListInfo
 *
 * The class of return value of ListObjects
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucket.html
 */
class InventoryListInfo
{

    private $isTruncated = null;
    private $nextContinuationToken = "";
    private $inventoryList = array();

    /**
     * InventoryListInfo constructor.
     * @param $isTruncated boolean
     * @param $nextContinuationToken string
     * @param array $inventoryList
     */
    public function __construct($isTruncated,$nextContinuationToken,array $inventoryList)
    {
        $this->isTruncated = $isTruncated;
        $this->nextContinuationToken = $nextContinuationToken;
        $this->inventoryList = $inventoryList;
    }

    /**
     * @return mixed
     */
    public function getIsTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * get nextContinuationToken
     * @return string
     */
    public function getNextContinuationToken()
    {
        return $this->nextContinuationToken;
    }


    /**
     * get the inventoryInfo list
     * @return InventoryInfo[]
     */
    public function getInventoryList()
    {
        return $this->inventoryList;
    }


}