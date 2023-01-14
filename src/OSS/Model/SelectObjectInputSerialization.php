<?php

namespace OSS\Model;
use OSS\Core\OssException;

/**
 * Class SelectObjectInputSerialization
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/74054.html
 */
class SelectObjectInputSerialization
{
	
	public $inputSerialization = array();
	public $type;
	
	public function __construct($type)
	{
		$this->inputSerialization = array();
		$this->type = strtoupper($type);
	}
	
	/**
	 * @param $type string
	 */
	public function addCompressionType($type){
		$this->inputSerialization['CompressionType'] = $type;
	}
	
	/**
	 * @param $headerInfo string
	 */
	public function addFileHeaderInfo($headerInfo)
	{
		if($this->type != "CSV"){
			throw new OssException("Type is not csv");
		}
		$this->inputSerialization[$this->type]['FileHeaderInfo'] = $headerInfo;
	}
	
	/**
	 * @param $recordDelimiter string
	 */
	public function addRecordDelimiter($recordDelimiter){
		$this->inputSerialization[$this->type]['RecordDelimiter'] = base64_encode($recordDelimiter);
	}
	
	/**
	 * @param $fieldDelimiter string
	 */
	public function addFieldDelimiter($fieldDelimiter){
		$this->inputSerialization[$this->type]['FieldDelimiter'] = base64_encode($fieldDelimiter);
	}
	
	/**
	 * @param $quoteCharacter string
	 */
	public function addQuoteCharacter($quoteCharacter){
		$this->inputSerialization[$this->type]['QuoteCharacter'] = base64_encode($quoteCharacter);
	}
	
	/**
	 * @param $commentCharacter string
	 */
	public function addCommentCharacter($commentCharacter){
		$this->inputSerialization[$this->type]['CommentCharacter'] = base64_encode($commentCharacter);
	}
	
	/**
	 * @param $range string
	 */
	public function addRange($range){
		$this->inputSerialization[$this->type]['Range'] =$range;
	}
	
	/**
	 * @param $allowQuotedRecordDelimiter boolean
	 */
	public function addAllowQuotedRecordDelimiter($allowQuotedRecordDelimiter){
		$this->inputSerialization[$this->type]['AllowQuotedRecordDelimiter'] =$allowQuotedRecordDelimiter;
	}
	
	/**
	 * @param $type string
	 */
	public function addJsonType($type){
		if($this->type != "JSON"){
			throw new OssException("Type is not json");
		}
		$this->inputSerialization[$this->type]['Type'] =$type;
	}
	
	
	/**
	 * @param $parseJsonNumberAsString boolean
	 */
	public function addParseJsonNumberAsString($parseJsonNumberAsString){
		if($this->type != "JSON"){
			throw new OssException("Type is not json");
		}
		$this->inputSerialization[$this->type]['ParseJsonNumberAsString'] = $parseJsonNumberAsString;
	}
	
}


