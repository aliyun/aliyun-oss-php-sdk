<?php

namespace OSS\Model;


/**
 * Cname Config Certificate
 *
 * Class CnameConfigCertificate
 * @package OSS\Model
 */
class CnameConfigCertificate
{
    /**
     * @var string
     */
    private $certId;
    /**
     * @var string
     */
    private $certificate;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $previousCertId;

    /**
     * @var boolean
     */
    private $force;

    /**
     * @var boolean
     */
    private $deleteCertificate;

    private $type;

    /**
     * @param $certId string
     */
    public function setCertId($certId){
        $this->certId = $certId;
    }

    /**
     * @return string
     */
    public function getCertId(){
        return $this->certId;
    }

    /**
     * @return string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @param $type string
     */
    public function setType($type){
        $this->type = $type;
    }

    /**
     * @param $certificate string
     */
    public function setCertificate($certificate){
        $this->certificate = $certificate;
    }

    /**
     * @return string
     */
    public function getCertificate(){
        return $this->certificate;
    }

    /**
     * @param $privateKey string
     */
    public function setPrivateKey($privateKey){
        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey(){
        return $this->privateKey;
    }

    /**
     * @param $previousCertId string
     */
    public function setPreviousCertId($previousCertId){
        $this->previousCertId = $previousCertId;
    }

    /**
     * @return string
     */
    public function getPreviousCertId(){
        return $this->previousCertId;
    }

    /**
     * @param $force boolean
     */
    public function setForce($force){
        $this->force = $force;
    }

    /**
     * @return boolean
     */
    public function getForce(){
        return $this->force;
    }

    /**
     * @param $deleteCertificate boolean
     */
    public function setDeleteCertificate($deleteCertificate){
        $this->deleteCertificate = $deleteCertificate;
    }

    /**
     * @return boolean
     */
    public function getDeleteCertificate(){
        return $this->deleteCertificate;
    }

    /**
     * @param \SimpleXMLElement $xmlCname
     */
    public function appendToXml($xmlCname)
    {
        if (isset($this->certId) || isset($this->certificate) || isset($this->privateKey) || isset($this->previousCertId) || isset($this->force) || isset($this->deleteCertificate)){
            $xmlCertificateConfig = $xmlCname->addChild('CertificateConfiguration');
        }
        if (isset($this->certId)){
            $xmlCertificateConfig->addChild('CertId', $this->certId);
        }
        if (isset($this->certificate)){
            $xmlCertificateConfig->addChild('Certificate',$this->certificate);
        }
        if (isset($this->privateKey)){
            $xmlCertificateConfig->addChild('PrivateKey',$this->privateKey);
        }
        if (isset($this->previousCertId)){
            $xmlCertificateConfig->addChild('PreviousCertId',$this->previousCertId);
        }
        if (isset($this->force)){
            $xmlCertificateConfig->addChild('Force',        ($this->force === true || $this->force === True ? "true" : "false"));
        }
        if (isset($this->deleteCertificate)){
            $xmlCertificateConfig->addChild('DeleteCertificate', ($this->deleteCertificate === true || $this->deleteCertificate === True ? "true" : "false"));
        }

    }

}