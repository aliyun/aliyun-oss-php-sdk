<?php

namespace OSS\Model;

/**
 * Class ObjectListInfoV2
 *
 * The class of return value of ListObjects
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucket.html
 */
class ObjectListInfoV2
{
    /**
     * ObjectListInfo constructor.
     *
     * @param string $bucketName
     * @param string $prefix
     * @param string $maxKeys
     * @param string $delimiter
     * @param null $isTruncated
     * @param array $objectList
     * @param array $prefixList
	 * @param string $nextContinuationToken
	 * @param string $startAfter
     */
    public function __construct($bucketName, $prefix, $maxKeys, $delimiter, $isTruncated, array $objectList, array $prefixList,$nextContinuationToken,$startAfter)
    {
        $this->bucketName = $bucketName;
        $this->prefix = $prefix;
        $this->maxKeys = $maxKeys;
        $this->delimiter = $delimiter;
        $this->isTruncated = $isTruncated;
        $this->objectList = $objectList;
        $this->prefixList = $prefixList;
		$this->nextContinuationToken = $nextContinuationToken;
		$this->startAfter = $startAfter;
    }

    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return int
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @return mixed
     */
    public function getIsTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * Get the ObjectInfo list.
     *
     * @return ObjectInfo[]
     */
    public function getObjectList()
    {
        return $this->objectList;
    }

    /**
     * Get the PrefixInfo list
     *
     * @return PrefixInfo[]
     */
    public function getPrefixList()
    {
        return $this->prefixList;
    }

	
	/**
	 * @return string
	 */
	public function getNextContinuationToken()
	{
		return $this->nextContinuationToken;
	}
	
	/**
	 * @return string
	 */
	public function getStartAfter()
	{
		return $this->startAfter;
	}

    private $bucketName = "";
    private $prefix = "";
    private $maxKeys = 0;
    private $delimiter = "";
    private $isTruncated = null;
    private $objectList = array();
    private $prefixList = array();
	private $nextContinuationToken = "";
	private $startAfter = "";
}