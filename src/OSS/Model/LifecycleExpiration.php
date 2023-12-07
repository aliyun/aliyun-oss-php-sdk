<?php

namespace OSS\Model;


/**
 * Class LifecycleExpiration
 * @package OSS\Model
 *
 */
class LifecycleExpiration
{

    /**
     * @var int|null
     */
    private $days;
    /**
     * @var string|null
     */
    private $date;
    /**
     * @var string|null
     */
    private $createdBeforeDate;
    /**
     * @var bool|null
     */
    private $expiredObjectDeleteMarker;

    /**
     * LifecycleExpiration constructor
     * @param $days int
     * @param $date string
     * @param $createdBeforeDate string
     * @param $expiredObjectDeleteMarker bool
     */
    public function __construct($days=null, $date=null, $createdBeforeDate=null, $expiredObjectDeleteMarker=null)
    {
        $this->days = $days;
        $this->date = $date;
        $this->createdBeforeDate = $createdBeforeDate;
        $this->expiredObjectDeleteMarker = $expiredObjectDeleteMarker;
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
     * Get date
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     * @param $date string
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * Get Expired Object DeleteMarker
     * @return bool
     */
    public function getExpiredObjectDeleteMarker()
    {
        return $this->expiredObjectDeleteMarker;
    }

    /**
     * Set Expired Object DeleteMarker
     * @param $expiredObjectDeleteMarker bool
     */
    public function setExpiredObjectDeleteMarker($expiredObjectDeleteMarker)
    {
        $this->expiredObjectDeleteMarker = $expiredObjectDeleteMarker;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if(isset($this->days) || isset($this->date) || isset($this->createdBeforeDate) || isset($this->expiredObjectDeleteMarker)){
            $xmlExpiration = $xmlRule->addChild("Expiration");
        }

        if (isset($this->days)){
            $xmlExpiration->addChild('Days', $this->days);
        }

        if (isset($this->date)){
            $xmlExpiration->addChild('Date', $this->date);
        }

        if (isset($this->createdBeforeDate)){
            $xmlExpiration->addChild('CreatedBeforeDate', $this->createdBeforeDate);
        }

        if (isset($this->expiredObjectDeleteMarker)){
            $xmlExpiration->addChild('ExpiredObjectDeleteMarker', is_bool($this->expiredObjectDeleteMarker) ? json_encode($this->expiredObjectDeleteMarker):$this->expiredObjectDeleteMarker);
        }
    }
}