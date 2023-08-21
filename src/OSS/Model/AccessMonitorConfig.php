<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class AccessMonitorConfig
 * @package OSS\Model
 */
class AccessMonitorConfig implements XmlConfig
{
	/**
	 * Parse AccessMonitorConfig from the xml.
	 * @param string $strXml
	 * @throws OssException
	 * @return null
	 */
	public function parseFromXml($strXml)
	{
		$xml = simplexml_load_string($strXml);
		if (isset($xml->Status)) {
			$this->status = strval($xml->Status);
		}
	}
	
	/**
	 * Serialize the object into xml string.
	 *
	 * @return string
	 */
	public function serializeToXml()
	{
		$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><AccessMonitorConfiguration></AccessMonitorConfiguration>');
		if (isset($this->status)) {
            $xml->addChild('Status',$this->status);
		}
		return $xml->asXML();
	}
	
	public function __toString()
	{
		return $this->serializeToXml();
	}
	

    /**
     * @return string
     */
	public function getStatus()
	{
		return $this->status;
	}

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    /**
     * @var string
     */
	private $status;
}


