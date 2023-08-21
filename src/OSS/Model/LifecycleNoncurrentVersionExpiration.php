<?php

namespace OSS\Model;


/**
 * Class LifecycleNonCurrentVersionExpiration
 * @package OSS\Model
 *
 */
class LifecycleNonCurrentVersionExpiration
{

    /**
     * @var int|null
     */
    private $nonCurrentDays;

    /**
     * LifecycleNonCurrentVersionExpiration constructor
     * @param $nonCurrentDays int
     */
    public function __construct($nonCurrentDays=null)
    {
        $this->nonCurrentDays = $nonCurrentDays;
    }
    /**
     * Get Non Current Days
     *
     * @return int
     */
    public function getNonCurrentDays()
    {
        return $this->nonCurrentDays;
    }

    /**
     * Set Non Current Days
     * @param $nonCurrentDays int
     */
    public function setNonCurrentDays($nonCurrentDays)
    {
        $this->nonCurrentDays = $nonCurrentDays;
    }

    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {

        if (isset($this->nonCurrentDays)){
            $xmlNonCurrentVersionExpiration = $xmlRule->addChild("NoncurrentVersionExpiration");
            $xmlNonCurrentVersionExpiration->addChild('NoncurrentDays', $this->nonCurrentDays);
        }
    }

}