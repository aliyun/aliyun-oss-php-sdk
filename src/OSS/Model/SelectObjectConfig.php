<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class ReplicationConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class SelectObjectConfig implements XmlConfig
{
    private $config;
	public function __construct()
	{
		$this->config = array();
	}
	
	/**
	 * @param $config array
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}
	
	/**
	 * @param $expression sql string
	 */
	public function addExpression($expression){
		$this->config['Expression'] = base64_encode($expression);
	}
	
	
	/**
	 * @param $inputSerialization SelectObjectInputSerialization
	 */
	public function addInputSerialization($inputSerialization){
		$this->config['InputSerialization'] = $inputSerialization->inputSerialization;
	}
	
	/**
	 * @param $outputSerialization SelectObjectOutputSerialization
	 */
	public function addOutputSerialization($outputSerialization){
		$this->config['OutputSerialization'] = $outputSerialization->outputSerialization;
	}
	
	/**
	 * @param $configs array
	 */
	
	public function addPrefixSet($prefix)
	{
		$this->config['PrefixSet']['Prefix'][] = $prefix;
	}
	
	
	/**
	 * @param $action string
	 */
	public function addOptions($options)
	{
		$this->config['Options'] = $options->options;
	}
	
	/**
	 * Parse the xml into this object.
	 *
	 * @param string $strXml
	 * @throws OssException
	 * @return null
	 */
	public function parseFromXml($strXml)
	{
		$this->config = array();
		$xml = simplexml_load_string($strXml);
		$xmlJson= json_encode($xml);
		$this->config = json_decode($xmlJson,true);
	}
	
	
	/**
	 * Serialize the object to xml
	 *
	 * @return string
	 */
	public function serializeToXml()
	{
		return $this->arrToXml($this->config,null,null,'SelectRequest');
	}
	
	/**
	 * array turn to xml
	 * @param $arr
	 * @param $dom
	 * @param $item
	 * @param string $rootNode
	 * @return string
	 */
	public function arrToXml($arr,$dom,$item,$rootNode="Configuration")
	{
		if (!$dom) {
			$dom = new \DOMDocument("1.0",'utf-8');
		}
		if (!$item) {
			$item = $dom->createElement($rootNode);
			$dom->appendChild($item);
		}
		foreach ($arr as $key => $val) {
			if(!is_string($key)){
				continue;
			}
			if($key != 'Prefix'){
				$node = $dom->createElement($key);
				$item->appendChild($node);
			}else{
				foreach ($val as $value){
					$node = $dom->createElement("Prefix",$value);
					$item->appendChild($node);
				}
			}
			if (!is_array($val)) {
				if(is_bool($val)){
					if($val == true){
						$text = $dom->createTextNode('true');
					}else{
						$text = $dom->createTextNode('false');
					}
				}else{
					$text = $dom->createTextNode($val);
				}
				$node->appendChild($text);
			} else {
				$this->arrToXml($val, $dom, $node);
			}
		}
		return $dom->saveXML(null,LIBXML_NOEMPTYTAG);
	}
	
	private function createXmlNode($item,$dom,$node){
		if (!is_array($item)) {
			if(is_bool($item)){
				if($item == true){
					$text = $dom->createTextNode('true');
				}else{
					$text = $dom->createTextNode('false');
				}
				
			}else{
				$text = $dom->createTextNode($item);
			}
			$node->appendChild($text);
		} else {
			$this->arrToXml($item, $dom, $node);
		}
	}
	
	/**
	 *  Serialize the object into xml string.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->serializeToXml();
	}
	
	/**
	 * @return array
	 */
	public function getConfig(){
		return $this->config;
	}
	
	public function getId(){
		return $this->config['Id'];
	}
}


