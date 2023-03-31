<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class CnameConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/428391.html
 */
class CnameConfig implements XmlConfig
{

    /**
     * @var CnameConfigCertificate
     */
    private $certificateConfig;


    /**
     * @var string
     */
    private $cname;

    /**
     * @param $cname string
     */
    public function setCname($cname)
    {
        $this->cname = $cname;
    }

    /**
     * @param $certificateConfig CnameConfigCertificate
     */
    public function setCertificateConfig($certificateConfig)
    {
        $this->certificateConfig = $certificateConfig;
    }

    /**
     * @return CnameConfigCertificate
     */
    public function getCertificateConfig()
    {
        return $this->certificateConfig;
    }


    /**
     * @return string
     */
    public function getCname(){
        return $this->cname;
    }

    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @return void|null
     */
    public function parseFromXml($strXml)
    {
        $this->cnameConfig = array();
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Cname)) return;
        $this->parseCname($xml->Cname);
    }

    /**
     * @param $xmlCname \SimpleXMLElement
     */
    private function parseCname($xmlCname)
    {
        if(isset($xmlCname)){
            if (isset($xmlCname->Domain)){
                $this->setCname(strval($xmlCname->Domain));
            }
            if (isset($xmlCname->CertificateConfiguration)){
                $this->parseCertificateConfiguration($xmlCname->CertificateConfiguration);
            }
        }

    }

    /**
     * @param $certificateConfiguration \SimpleXMLElement
     */
    private function parseCertificateConfiguration($certificateConfiguration)
    {
        if(isset($certificateConfiguration)){
            $certificateConfig = new CnameConfigCertificate();
            if (isset($certificateConfiguration->CertId)){
                $certificateConfig->setCertId(strval($certificateConfiguration->CertId));
            }
            if (isset($certificateConfiguration->Certificate)){
                $certificateConfig->setCertificate(strval($certificateConfiguration->Certificate));
            }
            if (isset($certificateConfiguration->PrivateKey)){
                $certificateConfig->setPrivateKey(strval($certificateConfiguration->PrivateKey));
            }
            if (isset($certificateConfiguration->PreviousCertId)){
                $certificateConfig->setPreviousCertId(strval($certificateConfiguration->PreviousCertId));
            }
            if (isset($certificateConfiguration->Force)){
                $certificateConfig->setForce(strval($certificateConfiguration->Force));
            }
            if (isset($certificateConfiguration->DeleteCertificate)){
                $certificateConfig->setDeleteCertificate(strval($certificateConfiguration->DeleteCertificate));
            }
            $this->setCertificateConfig($certificateConfig);
        }
    }

    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><BucketCnameConfiguration></BucketCnameConfiguration>');

        if (isset($this->cname) || isset($this->certificateConfig)){
            $xmlCname = $xml->addChild('Cname');
        }
        if (isset($this->cname)){
            $xmlCname->addChild('Domain',$this->cname);
        }

        if (isset($this->certificateConfig)){
            $this->certificateConfig->appendToXml($xmlCname);
        }

        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }

}