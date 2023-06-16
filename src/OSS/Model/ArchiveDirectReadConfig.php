<?php

namespace OSS\Model;

/**
 * Class ArchiveDirectReadConfig
 * @package OSS\Model
 */
class ArchiveDirectReadConfig implements XmlConfig
{
	/**
	 * Parse ArchiveDirectReadConfig from the xml.
	 * @param string $strXml
     * @return null
	 */
	public function parseFromXml($strXml)
	{
		$xml = simplexml_load_string($strXml);
		if (isset($xml->Enabled)) {
			$this->enabled = (strval($xml->Enabled) === 'TRUE' || strval($xml->Enabled) === 'true') ? true : false;
		}
	}
	
	/**
	 * Serialize the object into xml string.
	 *
	 * @return string
	 */
	public function serializeToXml()
	{
		$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ArchiveDirectReadConfiguration></ArchiveDirectReadConfiguration>');
		if (isset($this->enabled)) {
			if($this->enabled === true){
				$xml->addChild('Enabled','true');
			} else {
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
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

	/**
	 * @var $enabled boolean
	 */
	private $enabled = false;
}


