<?php

namespace OSS\Model;


/**
 * Class LifecycleAbortMultipartUpload
 * @package OSS\Model
 */
class LifecycleAbortMultipartUpload
{

    /**
     * @var int|null
     */
    private $days;
    /**
     * @var string|null
     */
    private $createdBeforeDate;

    /**
     * LifecycleAbortMultipartUpload constructor
     * @param null|int $days
     * @param null|string $createdBeforeDate
     */
    public function __construct($days=null,$createdBeforeDate=null)
    {
        $this->days = $days;
        $this->createdBeforeDate = $createdBeforeDate;
    }
    /**
     * Get Days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param $days int
     */
    public function setDays($days)
    {
        $this->days = $days;
    }

    /**
     * Get Created Before Date
     *
     * @return string
     */
    public function getCreatedBeforeDate()
    {
        return $this->createdBeforeDate;
    }

    /**
     * Set Created Before Date
     * @param $createdBeforeDate string
     */
    public function setCreatedBeforeDate($createdBeforeDate)
    {
        $this->createdBeforeDate = $createdBeforeDate;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if(isset($this->days) || isset($this->createdBeforeDate)){
            $xmlAbortMultipartUpload = $xmlRule->addChild("AbortMultipartUpload");
        }

        if (isset($this->days)){
            $xmlAbortMultipartUpload->addChild('Days', $this->days);
        }

        if (isset($this->createdBeforeDate)){
            $xmlAbortMultipartUpload->addChild('CreatedBeforeDate', $this->createdBeforeDate);
        }
    }
}