<?php

namespace OSS\Model;

/**
 * Class WebsiteRedirect
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteRedirect {

    const MIRROR = 'Mirror';
    const EXTERNAL ='External';
    const ALICDN ='AliCDN';

    const HTTP ='http';
    const HTTPS = 'https';

    /**
     * @var string Mirror|External|AliCDN
     */
    private $redirectType;

    /**
     * @var boolean
     */
    private $passQueryString;
    /**
     * @var string
     */
    private $mirrorURL;

    /**
     * @var boolean
     */
    private $mirrorPassQueryString;

    /**
     * @var boolean
     */
    private $mirrorFollowRedirect;

    /**
     * @var boolean
     */
    private $mirrorCheckMd5;

    /**
     * @var WebsiteMirrorHeaders
     */
    private $mirrorHeaders;

    /**
     * @var string
     */
    private $protocol;

    /**
     * @var string
     */
    private $hostName;

    /**
     * @var string
     */
    private $replaceKeyPrefixWith;

    /**
     * @var boolean
     */
    private $enableReplacePrefix;

    /**
     * @var string
     */
    private $replaceKeyWith;


    /**
     * @var int 301|302|307
     */
    private $httpRedirectCode;
    /**
     * @return string
     */
    public function getRedirectType(){
        return $this->redirectType;
    }


    /**
     * @param string $type Mirror|External|AliCDN
     */
    public function setRedirectType($type){
        $this->redirectType = $type;
    }

    /**
     * @param $mirrorUrl
     */
    public function setMirrorURL($mirrorUrl){
        $this->mirrorURL= $mirrorUrl;
    }

    /**
     * @return boolean
     */
    public function getPassQueryString(){
        return $this->passQueryString;
    }

    /**
     * @param $passQueryString bool
     */
    public function setPassQueryString($passQueryString){
        $this->passQueryString = $passQueryString;
    }

    /**
     * @return string
     */
    public function getProtocol(){
        return $this->protocol;
    }

    /**
     * @param $protocol string
     */
    public function setProtocol($protocol){
        $this->protocol = $protocol;
    }


    /**
     * @return string
     */
    public function getHostName(){
        return $this->hostName;
    }

    /**
     * @param $hostName string
     */
    public function setHostName($hostName){
        $this->hostName = $hostName;
    }

    /**
     * @return string
     */
    public function getReplaceKeyWith(){
        return $this->replaceKeyWith;
    }

    /**
     * @param $replaceKeyWith string
     */
    public function setReplaceKeyWith($replaceKeyWith){
        $this->replaceKeyWith = $replaceKeyWith;
    }

    /**
     * @param $replaceKeyPrefixWith string
     */
    public function setReplaceKeyPrefixWith($replaceKeyPrefixWith){
        $this->replaceKeyPrefixWith = $replaceKeyPrefixWith;
    }

    /**
     * @return string
     */
    public function getReplaceKeyPrefixWith(){
        return $this->replaceKeyPrefixWith;
    }

    /**
     * @return boolean
     */
    public function getEnableReplacePrefix(){
        return $this->enableReplacePrefix;
    }


    /**
     * @param $enableReplacePrefix boolean
     */
    public function setEnableReplacePrefix($enableReplacePrefix){
        $this->enableReplacePrefix = $enableReplacePrefix;
    }

    /**
     * @return string
     */
    public function getMirrorURL(){
        return $this->mirrorURL;
    }

    /**
     * @return int
     */
    public function getHttpRedirectCode(){
        return $this->httpRedirectCode;
    }


    /**
     * @param $httpRedirectCode int
     */
    public function setHttpRedirectCode($httpRedirectCode){
        $this->httpRedirectCode = $httpRedirectCode;
    }

    /**
     * @return boolean
     */
    public function getMirrorPassQueryString(){
        return $this->mirrorPassQueryString;
    }

    /**
     * @param $mirrorPassQueryString boolean
     */
    public function setMirrorPassQueryString($mirrorPassQueryString){
        $this->mirrorPassQueryString = $mirrorPassQueryString;
    }

    /**
     * @return boolean
     */
    public function getMirrorFollowRedirect(){
        return $this->mirrorFollowRedirect;
    }

    /**
     * @param $mirrorFollowRedirect boolean
     */
    public function setMirrorFollowRedirect($mirrorFollowRedirect){
        $this->mirrorFollowRedirect = $mirrorFollowRedirect;
    }

    /**
     * @return boolean
     */
    public function getMirrorCheckMd5(){
        return $this->mirrorCheckMd5;
    }

    /**
     * @param $mirrorCheckMd5 boolean
     */
    public function setMirrorCheckMd5($mirrorCheckMd5){
        $this->mirrorCheckMd5 = $mirrorCheckMd5;
    }

    /**
     * @return WebsiteMirrorHeaders
     */
    public function getMirrorHeaders(){
        return $this->mirrorHeaders;
    }
    /**
     * @param $mirrorHeaders WebsiteMirrorHeaders
     */
    public function setMirrorHeaders($mirrorHeaders){
        $this->mirrorHeaders = $mirrorHeaders;
    }

    /**
     * @param \SimpleXMLElement $xmlRedirect
     */
    public function appendToXml(&$xmlRedirect)
    {

        if (isset($this->redirectType)){
            $xmlRedirect->addChild("RedirectType",$this->redirectType);
        }
        if (isset($this->passQueryString)){
            $xmlRedirect->addChild("PassQueryString",json_encode($this->passQueryString));
        }
        if (isset($this->mirrorURL)){
            $xmlRedirect->addChild("MirrorURL",$this->mirrorURL);
        }
        if (isset($this->mirrorPassQueryString)){
            $xmlRedirect->addChild("MirrorPassQueryString",json_encode($this->mirrorPassQueryString));
        }
        if (isset($this->mirrorFollowRedirect)){
            $xmlRedirect->addChild("MirrorFollowRedirect",json_encode($this->mirrorFollowRedirect));
        }
        if (isset($this->mirrorCheckMd5)){
            $xmlRedirect->addChild("MirrorCheckMd5",json_encode($this->mirrorCheckMd5));
        }
        if (isset($this->mirrorHeaders)){
            $xmlMirrorHeaders = $xmlRedirect->addChild("MirrorHeaders");
            $this->mirrorHeaders->appendToXml($xmlMirrorHeaders);
        }
        if (isset($this->protocol)){
            $xmlRedirect->addChild("Protocol",$this->protocol);
        }
        if (isset($this->hostName)){
            $xmlRedirect->addChild("HostName",$this->hostName);
        }
        if (isset($this->replaceKeyPrefixWith)){
            $xmlRedirect->addChild("ReplaceKeyPrefixWith",$this->replaceKeyPrefixWith);
        }
        if (isset($this->enableReplacePrefix)){
            $xmlRedirect->addChild("EnableReplacePrefix",json_encode($this->enableReplacePrefix));
        }
        if (isset($this->replaceKeyWith)){
            $xmlRedirect->addChild("ReplaceKeyWith",$this->replaceKeyWith);
        }
        if (isset($this->httpRedirectCode)){
            $xmlRedirect->addChild("HttpRedirectCode",$this->httpRedirectCode);
        }
    }
}
