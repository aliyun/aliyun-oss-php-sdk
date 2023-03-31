<?php

namespace OSS\Model;


/**
 * Cname Certificate
 *
 * Class CnameCertificate
 * @package OSS\Model
 */
class CnameCertificate
{

    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $certId;
    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $creationDate;

    /**
     * @var string
     */
    private $fingerprint;

    /**
     * @var string
     */
    private $validStartDate;

    /**
     * @var string
     */
    private $validEndDate;

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
     * @param $status string
     */
    public function setStatus($status){
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * @param $creationDate string
     */
    public function setCreationDate($creationDate){
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function getCreationDate(){
        return $this->creationDate;
    }

    /**
     * @param $fingerprint string
     */
    public function setFingerprint($fingerprint){
        $this->fingerprint = $fingerprint;
    }

    /**
     * @return string
     */
    public function getFingerprint(){
        return $this->fingerprint;
    }

    /**
     * @param $validStartDate string
     */
    public function setValidStartDate($validStartDate){
        $this->validStartDate = $validStartDate;
    }

    /**
     * @return string
     */
    public function getValidStartDate(){
        return $this->validStartDate;
    }

    /**
     * @param $validEndDate string
     */
    public function setValidEndDate($validEndDate){
        $this->validEndDate = $validEndDate;
    }

    /**
     * @return string
     */
    public function getValidEndDate(){
        return $this->validEndDate;
    }

    /**
     * @param \SimpleXMLElement $xmlCname
     */
    public function appendToXml(&$xmlCname)
    {
        if (isset($this->certId) || isset($this->type) || isset($this->status) || isset($this->creationDate) || isset($this->fingerprint) || isset($this->validStartDate) || isset($this->validEndDate)){
            $xmlCertificate = $xmlCname->addChild('Certificate');
        }
        if (isset($this->type)){
            $xmlCertificate->addChild('Type',$this->type);
        }
        if (isset($this->certId)){
            $xmlCertificate->addChild('CertId', $this->certId);
        }
        if (isset($this->status)){
            $xmlCertificate->addChild('Status',$this->status);
        }
        if (isset($this->creationDate)){
            $xmlCertificate->addChild('CreationDate',$this->creationDate);
        }
        if (isset($this->fingerprint)){
            $xmlCertificate->addChild('Fingerprint',$this->fingerprint);
        }
        if (isset($this->validStartDate)){
            $xmlCertificate->addChild('ValidStartDate',$this->validStartDate);
        }
        if (isset($this->validEndDate)){
            $xmlCertificate->addChild('ValidEndDate',$this->validEndDate);
        }

    }

}