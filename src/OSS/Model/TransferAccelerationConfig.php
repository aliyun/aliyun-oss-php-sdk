<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class TransferAccelerationConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/214603.html
 */
class TransferAccelerationConfig implements XmlConfig
{
	
	/**
	 * TransferAccelerationConfig constructor.
	 * @param bool $enabled true
	 */
	public function __construct($enabled=true)
	{
		$this->enabled = $enabled;
	}
	
	/**
	 * Parse TransferAccelerationConfig from the xml.
	 * @param string $strXml
	 * @throws OssException
	 * @return null
	 */
	public function parseFromXml($strXml)
	{
		$xml = simplexml_load_string($strXml);
		if (isset($xml->enabled)) {
			$this->enabled = $xml->enabled;
		}
	}
	
	/**
	 * Serialize the object into xml string.
	 *
	 * @return string
	 */
	public function serializeToXml()
	{
		$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><TransferAccelerationConfiguration></TransferAccelerationConfiguration>');
		if (isset($this->enabled)) {
			if($this->enabled === true){
				$xml->addChild('Enabled','true');
			}
			if($this->enabled === false){
				$xml->addChild('Enabled','false');
			}
			
		}
		return $xml->asXML();
	}
	
	public function __toString()
	{
		return $this->serializeToXml();
	}
	
	
	/**
	 * @return bool
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}
	
	/**
	 * @var $enabled boolean
	 */
	private $enabled;
}


