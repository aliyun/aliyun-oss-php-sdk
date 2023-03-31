<?php

namespace OSS\Model;

/**
 * Class CnameInfo
 * @package OSS\Model
 */
class CnameInfo
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var CnameCertificate
     */
    private $certificate;

    /**
     * @var string
     */
    private $lastModified;
    /**
     * @var string
     */
    private $status;

    /**
     * Get domain name
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain name
     * @param $domain string
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Get Cname Certificate
     *
     * @return CnameCertificate
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * Add Cname Certificate
     *
     * @param $certificate CnameCertificate
     */
    public function addCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @param \SimpleXMLElement $xmlCname
     */
    public function appendToXml(&$xmlCname)
    {
        if (isset($this->domain)){
            $xmlCname->addChild('Domain', $this->domain);
        }
        if (isset($this->certificateConfiguration)){
            $this->certificateConfiguration->appendToXml($xmlCname);
        }

        if (isset($this->certificate)){
            $this->certificate->appendToXml($xmlCname);
        }

    }

    /**
     * Get lastModified
     *
     * @return string
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set lastModified
     *
     * @param $lastModified string
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }


    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param $status string
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}