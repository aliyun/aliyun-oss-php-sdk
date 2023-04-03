<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class WebsiteConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteConfig implements XmlConfig
{
    /**
     * @var WebsiteIndexDocument
     */
    private $indexDocument;
    /**
     * @var WebsiteErrorDocument
     */
    private $errorDocument;
    /**
     * @var WebsiteRoutingRule[]
     */
    private $routingRules;

    const OSS_MAX_RULES = 20;

    private $suffix;
    private $key;

    /**
     * WebsiteConfig constructor.
     * @param  string|WebsiteIndexDocument $indexDocument
     * @param  string|WebsiteErrorDocument $errorDocument
     */
    public function __construct($indexDocument = null, $errorDocument = null)
    {
        if (is_string($indexDocument)){
            if ($indexDocument != ""){
                $this->suffix = $indexDocument;
            }
            $this->indexDocument = new WebsiteIndexDocument();
        }
        if (is_string($errorDocument)){
            if ($errorDocument != ""){
                $this->key = $errorDocument;
            }
            $this->errorDocument = new WebsiteErrorDocument();
        }

        if (is_object($indexDocument)){
            $this->indexDocument = $indexDocument;
        }
        if (is_object($errorDocument)){
            $this->errorDocument = $errorDocument;
        }

    }

    /**
     * @param WebsiteIndexDocument $indexDocument
     */
    public function setIndexDocument($indexDocument){
        $this->indexDocument = $indexDocument;
    }

    /**
     * @param WebsiteErrorDocument $errorDocument
     */
    public function setErrorDocument($errorDocument){
        $this->errorDocument = $errorDocument;
    }

    /**
     * @param string $strXml
     * @return null
     * @throws \Exception
     */
    public function parseFromXml($strXml)
    {
        $xml = new \SimpleXMLElement($strXml);
        if (!isset($xml->IndexDocument) && !isset($xml->ErrorDocument) && !isset($xml->RoutingRules)) return;
        if (isset($xml->IndexDocument)){
            $this->parseIndexDocument($xml->IndexDocument);
        }
        if (isset($xml->ErrorDocument)){
            $this->parseErrorDocument($xml->ErrorDocument);
        }
        if (isset($xml->RoutingRules)){
            $this->parseRoutingRules($xml->RoutingRules);
        }
    }

    /**
     * @param $indexDocument \SimpleXMLElement
     */
    private function parseIndexDocument($indexDocument)
    {
        if(isset($indexDocument)){
            $index = new WebsiteIndexDocument();
            if (isset($indexDocument->Suffix)){
                $index->setSuffix(strval($indexDocument->Suffix));
            }
            if (isset($indexDocument->SupportSubDir)){
                $index->setSupportSubDir(strval($indexDocument->SupportSubDir) === "TRUE" || strval($indexDocument->SupportSubDir) === "true");
            }
            if (isset($indexDocument->Type)){
                $index->setType(strval($indexDocument->Type));
            }
            $this->setIndexDocument($index);
        }
    }

    /**
     * @param $errorDocument \SimpleXMLElement
     */
    private function parseErrorDocument($errorDocument)
    {
        if(isset($errorDocument)){
            $error = new WebsiteErrorDocument();
            if (isset($errorDocument->Key)){
                $error->setKey(strval($errorDocument->Key));
            }
            if (isset($errorDocument->HttpStatus)){
                $error->setHttpStatus(intval($errorDocument->HttpStatus));
            }
            $this->setErrorDocument($error);
        }
    }

    /**
     * @param $routingRules
     * @throws OssException
     */
    private function parseRoutingRules($routingRules)
    {
        if(isset($routingRules)){
            foreach ($routingRules->RoutingRule as $rule){
                $routingRule = new WebsiteRoutingRule();
                if (isset($rule->RuleNumber)){
                    $routingRule->setNumber(intval($rule->RuleNumber));
                }
                if (isset($rule->Condition)){
                    $this->parseCondition($rule->Condition,$routingRule);
                }
                if (isset($rule->Redirect)){
                    $this->parseRedirect($rule->Redirect,$routingRule);
                }
                $this->addRule($routingRule);
            }
        }

    }

    /**
     * @param $condition \SimpleXMLElement
     * @param $websiteRoutingRule WebsiteRoutingRule
     */
    private function parseCondition($condition,&$websiteRoutingRule)
    {
        $websiteCondition = new WebsiteCondition();
        if (isset($condition->KeyPrefixEquals)){
            $websiteCondition->setKeyPrefixEquals(strval($condition->KeyPrefixEquals));
        }
        if (isset($condition->HttpErrorCodeReturnedEquals)){
            $websiteCondition->setHttpErrorCodeReturnedEquals(strval($condition->HttpErrorCodeReturnedEquals));
        }
        if (isset($condition->KeySuffixEquals)){
            $websiteCondition->setKeySuffixEquals(strval($condition->KeySuffixEquals));
        }
        if (isset($condition->IncludeHeader)){
            $this->parseIncludeHeaders($condition->IncludeHeader,$websiteCondition);
        }
        $websiteRoutingRule->setCondition($websiteCondition);
    }


    /**
     * @param $includeHeaders \SimpleXMLElement
     * @param $websiteCondition WebsiteCondition
     */
    private function parseIncludeHeaders($includeHeaders,&$websiteCondition)
    {
        foreach ($includeHeaders as $header){
            $websiteIncludeHeader = new WebsiteIncludeHeader();
            if (isset($header->Key)){
                $websiteIncludeHeader->setKey(strval($header->Key));
            }

            if (isset($header->Equals)){
                $websiteIncludeHeader->setEquals(strval($header->Equals));
            }

            $websiteCondition->addIncludeHeader($websiteIncludeHeader);
        }
    }


    /**
     * @param $mirrorHeaders
     * @param $websiteRedirect WebsiteRedirect
     * @throws OssException
     */
    private function parseMirrorHeaders($mirrorHeaders,&$websiteRedirect)
    {
        $websiteMirrorHeaders= new WebsiteMirrorHeaders();
        if (isset($mirrorHeaders->PassAll)){
            $websiteMirrorHeaders->setPassAll(strval($mirrorHeaders->PassAll) === 'true' || strval($mirrorHeaders->PassAll) === 'TRUE');
        }

        if (isset($mirrorHeaders->Pass)){
            foreach ($mirrorHeaders->Pass as $pass){
                $websiteMirrorHeaders->addPass(strval($pass));
            }

        }

        if (isset($mirrorHeaders->Remove)){
            foreach ($mirrorHeaders->Remove as $remove){
                $websiteMirrorHeaders->addRemove(strval($remove));
            }
        }

        if (isset($mirrorHeaders->Set)){
            $this->parseSet($mirrorHeaders->Set,$websiteMirrorHeaders);
        }
        $websiteRedirect->setMirrorHeaders($websiteMirrorHeaders);
    }


    /**
     * @param $set \SimpleXMLElement
     * @param $websiteMirrorHeaders WebsiteMirrorHeaders
     * @throws OssException
     */
    private function parseSet($set,&$websiteMirrorHeaders)
    {
        if (isset($set)){
            foreach ($set as $headerSet){
                $websiteMirrorHeadersSet = new WebsiteMirrorHeadersSet();
                if (isset($headerSet->Key)){
                    $websiteMirrorHeadersSet->setKey(strval($headerSet->Key));
                }
                if (isset($headerSet->Value)){
                    $websiteMirrorHeadersSet->setValue(strval($headerSet->Value));
                }
                $websiteMirrorHeaders->addSet($websiteMirrorHeadersSet);
            }
        }
    }

    /**
     * @param $redirect \SimpleXMLElement
     * @param $websiteRoutingRule WebsiteRoutingRule
     * @throws OssException
     */
    private function parseRedirect($redirect,&$websiteRoutingRule)
    {
        $websiteRedirect = new WebsiteRedirect();
        if (isset($redirect->RedirectType)){
            $websiteRedirect->setRedirectType(strval($redirect->RedirectType));
        }
        if (isset($redirect->MirrorURL)){
            $websiteRedirect->setMirrorURL(strval($redirect->MirrorURL));
        }
        if (isset($redirect->PassQueryString)){
            $websiteRedirect->setPassQueryString(strval($redirect->PassQueryString) === 'true' || strval($redirect->PassQueryString) === 'TRUE');
        }
        if (isset($redirect->Protocol)){
            $websiteRedirect->setProtocol(strval($redirect->Protocol));
        }
        if (isset($redirect->ReplaceKeyWith)){
            $websiteRedirect->setReplaceKeyWith(strval($redirect->ReplaceKeyWith));
        }
        if (isset($redirect->HostName)){
            $websiteRedirect->setHostName(strval($redirect->HostName));
        }
        if (isset($redirect->EnableReplacePrefix)){
            $websiteRedirect->setEnableReplacePrefix(strval($redirect->EnableReplacePrefix) === 'TRUE' || strval($redirect->EnableReplacePrefix) === 'true');
        }
        if (isset($redirect->HttpRedirectCode)){
            $websiteRedirect->setHttpRedirectCode(strval($redirect->HttpRedirectCode));
        }
        if (isset($redirect->MirrorPassQueryString)){
            $websiteRedirect->setMirrorPassQueryString(strval($redirect->MirrorPassQueryString) === "TRUE" || strval($redirect->MirrorPassQueryString) === "true");
        }
        if (isset($redirect->MirrorFollowRedirect)){
            $websiteRedirect->setMirrorFollowRedirect(strval($redirect->MirrorFollowRedirect) === "TRUE" || strval($redirect->MirrorFollowRedirect) === "true" );
        }
        if (isset($redirect->MirrorCheckMd5)){
            $websiteRedirect->setMirrorCheckMd5(strval($redirect->MirrorCheckMd5) === 'true' || strval($redirect->MirrorCheckMd5) === 'TRUE');
        }

        if (isset($redirect->MirrorHeaders)){
            $this->parseMirrorHeaders($redirect->MirrorHeaders,$websiteRedirect);
        }
        $websiteRoutingRule->setRedirect($websiteRedirect);
    }

    /**
     * Serialize the WebsiteConfig object into xml string.
     *
     * @return string
     * @throws OssException
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><WebsiteConfiguration></WebsiteConfiguration>');

        if (isset($this->indexDocument) || isset($this->suffix)){
            $xmlIndexDocument = $xml->addChild('IndexDocument');
            if (isset($this->suffix)){
                $xmlIndexDocument->addChild('Suffix', $this->suffix);
            }
            $this->indexDocument->appendToXml($xmlIndexDocument);
        }
        if (isset($this->errorDocument) || isset($this->key)) {
            $xmlErrorDocument = $xml->addChild('ErrorDocument');
            if (isset($this->key)){
                $xmlErrorDocument->addChild('Key', $this->key);
            }
            $this->errorDocument->appendToXml($xmlErrorDocument);
        }
        if (isset($this->routingRules)){
            $xmlRoutingRules = $xml->addChild('RoutingRules');
            foreach ($this->routingRules as $rule){
                $xmlRoutingRule = $xmlRoutingRules->addChild('RoutingRule');
                $rule->appendToXml($xmlRoutingRule);
            }

        }
        return $xml->asXML();
    }

    /**
     * @param $rule WebsiteRoutingRule
     * @throws OssException
     */
    public function addRule($rule){
        if (isset($this->routingRules) && count($this->routingRules) >= self::OSS_MAX_RULES) {
            throw new OssException(
                "num of routing rules in the config exceeds : " . strval(self::OSS_MAX_RULES));
        }
        $this->routingRules[] = $rule;
    }

    /**
     * @return WebsiteIndexDocument|null
     */
    public function getIndexDocument(){
        return $this->indexDocument;
    }

    /**
     * @return WebsiteErrorDocument|null
     */
    public function getErrorDocument(){
        return $this->errorDocument;
    }

    /**
     * @return WebsiteRoutingRule[]
     */
    public function getRoutingRules(){
        return $this->routingRules;
    }

    /**
     *  Serialize the object into xml string.
     *
     * @return string
     * @throws OssException
     */
    public function __toString()
    {
        return $this->serializeToXml();
    }
}