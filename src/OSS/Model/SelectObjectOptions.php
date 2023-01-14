<?php

namespace OSS\Model;


/**
 * Class SelectObjectOptions
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/74054.html
 */
class SelectObjectOptions
{
	
	public $options = array();
	public function __construct()
	{
		$this->options = array();
	}
	
	/**
	 * @param $skipPartialDataRecord boolean
	 */
	public function addSkipPartialDataRecord($skipPartialDataRecord){
		$this->options['SkipPartialDataRecord'] = $skipPartialDataRecord;
	}
	
	/**
	 * @param $maxSkippedRecordsAllowed int
	 */
	public function addMaxSkippedRecordsAllowed($maxSkippedRecordsAllowed){
		$this->options['MaxSkippedRecordsAllowed'] = $maxSkippedRecordsAllowed;
	}
}


