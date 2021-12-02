<?php

namespace OSS\Model;

/**
 * Class OwnerInfo
 *
 * ListObjects return owner list of classes
 * The returned data contains two arrays
 * One is to get the list of objects【Can be understood as the corresponding file system file list】
 * One is to get owner list
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucketV2.html
 */
class OwnerInfo
{
	/**
	 * OwnerInfo constructor.
	 * @param $id string
	 * @param $displayName string
	 */
    public function __construct($id,$displayName)
    {
        $this->id = $id;
		$this->displayName = $displayName;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
	/**
	 * @return string
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

    private $id;
	private $displayName;
}