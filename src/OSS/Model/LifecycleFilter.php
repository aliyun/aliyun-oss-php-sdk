<?php

namespace OSS\Model;


/**
 * Class LifecycleFilter
 * @package OSS\Model
 */
class LifecycleFilter
{

    /**
     * @var LifecycleNot[]|null
     */
    private $not;

    /**
     * @var int|null
     */
    private $objectSizeGreaterThan;

    /**
     * @var int|null
     */
    private $objectSizeLessThan;

    /**
     * LifecycleFilter constructor.
     * @param LifecycleNot[] $not
     */
    public function __construct($not=null,$objectSizeGreaterThan=null,$objectSizeLessThan=null)
    {
        $this->not = $not;
        $this->objectSizeGreaterThan = $objectSizeGreaterThan;
        $this->objectSizeLessThan = $objectSizeLessThan;
    }

    /**
     * Get Filter Not
     *
     * @return LifecycleNot[]
     */
    public function getNot()
    {
        return $this->not;
    }

    /**
     * Get Object Size Greater Than
     *
     * @return int|null
     */
    public function getObjectSizeGreaterThan()
    {
        return $this->objectSizeGreaterThan;
    }

    /**
     * Get Object Size Less Than
     *
     * @return int|null
     */
    public function getObjectSizeLessThan()
    {
        return $this->objectSizeLessThan;
    }

    /**
     * Set Filter Not
     * @param LifecycleNot $not
     */
    public function addNot($not)
    {
        $this->not[] = $not;

    }

    /**
     * set Object Size Greater Than
     *
     * @param $objectSizeGreaterThan int
     */
    public function setObjectSizeGreaterThan($objectSizeGreaterThan)
    {
        $this->objectSizeGreaterThan = $objectSizeGreaterThan;
    }

    /**
     * set Object Size Less Than
     *
     * @param $objectSizeLessThan int
     */
    public function setObjectSizeLessThan($objectSizeLessThan)
    {
        $this->objectSizeLessThan = $objectSizeLessThan;
    }


    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if(isset($this->not) || isset($this->objectSizeGreaterThan) || isset($this->objectSizeLessThan)){
            $xmlFilter = $xmlRule->addChild("Filter");
            if (isset($this->not)){
                foreach ($this->not as $not){
                    $not->appendToXml($xmlFilter);
                }
            }

            if (isset($this->objectSizeGreaterThan)){
                $xmlFilter->addChild("ObjectSizeGreaterThan",$this->objectSizeGreaterThan);
            }

            if (isset($this->objectSizeLessThan)){
                $xmlFilter->addChild("ObjectSizeLessThan",$this->objectSizeLessThan);
            }
        }


    }
}