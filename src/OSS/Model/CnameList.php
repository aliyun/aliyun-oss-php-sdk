<?php

namespace OSS\Model;

/**
 * Class CnameList
 * @package OSS\Model
 */
class CnameList
{

    /**
     * bucket name
     *
     * @var string
     */
    private $bucket;

    /**
     * cname list
     *
     * @var CnameInfo[]
     */
    private $cnameList = array();

    /**
     * owner
     * @var string
     */
    private $owner;

    /**
     * Get bucket name
     *
     * @return string
     */
    public function getBucket()
    {
        return $this->bucket;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get token.
     *
     * @return CnameInfo[]
     */
    public function getCnameList()
    {
        return $this->cnameList;
    }

    /**
     * @param $cname CnameInfo
     */
    public function addCnameList($cname){
        $this->cnameList[] = $cname;
    }

    /**
     * Parse cname list from the xml.
     * @param $strXml string
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (isset($xml->Bucket) ) {
            $this->bucket = strval($xml->Bucket);
        }
        if (isset($xml->Owner) ) {
            $this->owner = strval($xml->Owner);
        }
        if (isset($xml->Cname) ) {
            $this->parseCname($xml->Cname);
        }
    }

    /**
     * @param $cname SimpleXMLElement
     */
    private function parseCname($cname){
        if(isset($cname)){
            foreach ($cname as $cnameInfo){
                $cnameConfig = new CnameInfo();
                if (isset($cnameInfo->Domain)){
                    $cnameConfig->setDomain(strval($cnameInfo->Domain));
                }
                if (isset($cnameInfo->LastModified)){
                    $cnameConfig->setLastModified(strval($cnameInfo->LastModified));
                }
                if (isset($cnameInfo->Status)){
                    $cnameConfig->setStatus(strval($cnameInfo->Status));
                }
                if (isset($cnameInfo->Certificate)){
                    $this->parseCertificate($cnameInfo->Certificate,$cnameConfig);
                }
                $this->addCnameList($cnameConfig);
            }
        }
    }


    /**
     * @param $certificate SimpleXMLElement
     * @param $cnameInfo CnameInfo
     */
    private function parseCertificate($certificate,&$cnameInfo){
        if(isset($certificate)){
            $certificateConfig = new CnameCertificate();
            if (isset($certificate->Type)){
                $certificateConfig->setType(strval($certificate->Type));
            }
            if (isset($certificate->CertId)){
                $certificateConfig->setCertId(strval($certificate->CertId));
            }
            if (isset($certificate->Status)){
                $certificateConfig->setStatus(strval($certificate->Status));
            }
            if (isset($certificate->CreationDate)){
                $certificateConfig->setCreationDate(strval($certificate->CreationDate));
            }
            if (isset($certificate->Fingerprint)){
                $certificateConfig->setFingerprint(strval($certificate->Fingerprint));
            }
            if (isset($certificate->ValidStartDate)){
                $certificateConfig->setValidStartDate(strval($certificate->ValidStartDate));
            }
            if (isset($certificate->ValidEndDate)){
                $certificateConfig->setValidEndDate(strval($certificate->ValidEndDate));
            }
            $cnameInfo->addCertificate($certificateConfig);
        }
    }

    public function serializeToXml()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><ListCnameResult></ListCnameResult>');

        if (isset($this->bucket)){
            $xml->addChild("Bucket",$this->bucket);
        }

        if (isset($this->owner)){
            $xml->addChild("Owner",$this->owner);
        }

        if (isset($this->cnameList)){
            foreach ($this->cnameList as $cname) {
                $xmlCname = $xml->addChild('Cname');
                $cname->appendToXml($xmlCname);
            }
        }

        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }

}