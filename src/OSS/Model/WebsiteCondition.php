<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class WebsiteCondition
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteCondition {

    /**
     * @var string
     */
    private $keyPrefixEquals;

    /**
     * @var int
     */
    private $httpErrorCodeReturnedEquals;

    /**
     * @var WebsiteIncludeHeader[]
     */
    private $includeHeader;

    /**
     * @var string
     */
    private $keySuffixEquals;


    const OSS_MAX_HEADER = 10;

    /**
     * @return string
     */
    public function getHttpErrorCodeReturnedEquals(){
        return $this->httpErrorCodeReturnedEquals;
    }

    /**
     * @param $httpErrorCodeReturnedEquals int
     */
    public function setHttpErrorCodeReturnedEquals($httpErrorCodeReturnedEquals){
        $this->httpErrorCodeReturnedEquals = $httpErrorCodeReturnedEquals;
    }

    /**
     * @return string
     */
    public function getKeyPrefixEquals(){
        return $this->keyPrefixEquals;
    }

    /**
     * @param $keyPrefixEquals string
     */
    public function setKeyPrefixEquals($keyPrefixEquals){
        $this->keyPrefixEquals = $keyPrefixEquals;
    }

    /**
     * @return string
     */
    public function getKeySuffixEquals(){
        return $this->keySuffixEquals;
    }


    /**
     * @param $keySuffixEquals string
     */
    public function setKeySuffixEquals($keySuffixEquals){
        $this->keySuffixEquals = $keySuffixEquals;
    }

    /**
     * @return WebsiteIncludeHeader[]
     */
    public function getIncludeHeader(){
        return $this->includeHeader;
    }


    /**
     * @param $includeHeaders WebsiteIncludeHeader
     * @throws OssException
     */
    public function addIncludeHeader($includeHeaders){
        if (isset($this->includeHeader) && count($this->includeHeader) >= self::OSS_MAX_HEADER) {
            throw new OssException(
                "num of include header in the config exceeds : " . strval(self::OSS_MAX_HEADER));
        }
        $this->includeHeader[] = $includeHeaders;
    }

    /**
     * @param \SimpleXMLElement $xmlCondition
     */
    public function appendToXml(&$xmlCondition)
    {
        if (isset($this->keyPrefixEquals)){
            $xmlCondition->addChild('KeyPrefixEquals', $this->keyPrefixEquals);
        }
        if (isset($this->httpErrorCodeReturnedEquals)){
            $xmlCondition->addChild('HttpErrorCodeReturnedEquals', $this->httpErrorCodeReturnedEquals);
        }
        if (isset($this->includeHeader)){
            foreach ($this->includeHeader as $includeHeader){
                $xmlIncludeHeader = $xmlCondition->addChild('IncludeHeader');
                $includeHeader->appendToXml($xmlIncludeHeader);
            }
        }
        if (isset($this->keySuffixEquals)){
            $xmlCondition->addChild('KeySuffixEquals', $this->keySuffixEquals);
        }
    }

}