<?php

namespace OSS\Model;

/**
 * Class RefererConfig
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/PutBucketReferer.html
 */
class RefererConfig implements XmlConfig
{
    /**
     * @param string $strXml
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->AllowEmptyReferer)) return;
        if (!isset($xml->RefererList)) return;
        $this->allowEmptyReferer = strval($xml->AllowEmptyReferer) === 'TRUE' || strval($xml->AllowEmptyReferer) === 'true';
        if (isset($xml->AllowTruncateQueryString)){
            $this->allowTruncateQueryString = strval($xml->AllowTruncateQueryString) === 'TRUE' || strval($xml->AllowTruncateQueryString) === 'true';
        }
        foreach ($xml->RefererList->Referer as $key => $refer) {
            $this->refererList[] = strval($refer);
        }

        if (isset($xml->RefererBlacklist->Referer)){
            foreach ($xml->RefererBlacklist->Referer as $refer) {
                $this->refererBlacklist[] = strval($refer);
            }
        }
    }


    /**
     * serialize the RefererConfig object into xml string
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><RefererConfiguration></RefererConfiguration>');
        if (isset($this->allowEmptyReferer)){
            $xml->addChild('AllowEmptyReferer',json_encode($this->allowEmptyReferer));
        }
        if (isset($this->allowTruncateQueryString)){
            $xml->addChild('AllowTruncateQueryString',json_encode($this->allowTruncateQueryString));
        }
        $refererList = $xml->addChild('RefererList');
        foreach ($this->refererList as $referer) {
            $refererList->addChild('Referer', $referer);
        }
        if (isset($this->refererBlacklist)){
            $refererList = $xml->addChild('RefererBlacklist');
            foreach ($this->refererBlacklist as $referer) {
                $refererList->addChild('Referer', $referer);
            }
        }
        return $xml->asXML();
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->serializeToXml();
    }

    /**
     * @param boolean $allowEmptyReferer
     */
    public function setAllowEmptyReferer($allowEmptyReferer)
    {
        $this->allowEmptyReferer = $allowEmptyReferer;
    }

    /**
     * @param boolean $allowTruncateQueryString
     */
    public function setAllowTruncateQueryString($allowTruncateQueryString)
    {
        $this->allowTruncateQueryString = $allowTruncateQueryString;
    }

    /**
     * @param string $referer
     */
    public function addReferer($referer)
    {
        $this->refererList[] = $referer;
    }

    /**
     * @param string $referer
     */
    public function addBlackReferer($referer){
        $this->refererBlacklist[] = $referer;
    }

    /**
     * @return boolean
     */
    public function getAllowEmptyReferer()
    {
        return $this->allowEmptyReferer;
    }


    /**
     * @return bool
     */
    public function getAllowTruncateQueryString(){
        return $this->allowTruncateQueryString;
    }

    /**
     * @return array
     */
    public function getRefererList()
    {
        return $this->refererList;
    }

    /**
     * @return array
     */
    public function getRefererBlacklist()
    {
        return $this->refererBlacklist;
    }

    /**
     * @var bool
     */
    private $allowEmptyReferer = true;
    /**
     * @var bool
     */
    private $allowTruncateQueryString;
    /**
     * @var array
     */
    private $refererList = array();
    /**
     * @var array
     */
    private $refererBlacklist;
}