<?php

namespace OSS\Model;


/**
 * Class SelectObjectInputSerialization
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/74054.html
 */
class SelectObjectOutputSerialization
{
	
	public $outputSerialization = array();
	public $type = "";
	public function __construct($type)
	{
		$this->type = strtoupper($type);
		$this->outputSerialization = array();
	}
	
	/**
	 * @param $keepAllColumns boolean
	 */
	public function addKeepAllColumns($keepAllColumns){
		$this->outputSerialization['KeepAllColumns'] = $keepAllColumns;
	}
	
	
	/**
	 * @param $outputRawData boolean
	 */
	public function addOutputRawData($outputRawData){
		$this->outputSerialization['OutputRawData'] = $outputRawData;
	}
	
	
	/**
	 * @param $enablePayloadCrc boolean
	 */
	public function addEnablePayloadCrc($enablePayloadCrc){
		$this->outputSerialization['EnablePayloadCrc'] = $enablePayloadCrc;
	}
	
	/**
	 * @param $recordDelimiter string
	 */
	public function addRecordDelimiter($recordDelimiter){
		$this->outputSerialization[$this->type]['RecordDelimiter'] = base64_encode($recordDelimiter);
	}
	
	/**
	 * @param $fieldDelimiter string
	 */
	public function addFieldDelimiter($fieldDelimiter){
		$this->outputSerialization[$this->type]['FieldDelimiter'] = base64_encode($fieldDelimiter);
	}
	
	/**
	 * @param $quoteCharacter string
	 */
	public function addQuoteCharacter($quoteCharacter){
		$this->outputSerialization[$this->type]['QuoteCharacter'] = base64_encode($quoteCharacter);
	}
	
	/**
	 * @param $commentCharacter string
	 */
	public function addCommentCharacter($commentCharacter){
		$this->outputSerialization[$this->type]['CommentCharacter'] = base64_encode($commentCharacter);
	}
	
	/**
	 * @param $range string
	 */
	public function addRange($range){
		$this->outputSerialization[$this->type]['Range'] =$range;
	}
	
	/**
	 * @param $allowQuotedRecordDelimiter boolean
	 */
	public function addAllowQuotedRecordDelimiter($allowQuotedRecordDelimiter){
		$this->outputSerialization[$this->type]['AllowQuotedRecordDelimiter'] =$allowQuotedRecordDelimiter;
	}
	
	/**
	 * @param $outputHeader boolean
	 */
	public function addOutputHeader($outputHeader){
		$this->outputSerialization['OutputHeader'] = base64_encode($outputHeader);
	}
	
}


