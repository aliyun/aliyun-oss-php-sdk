<?php

namespace OSS\Model;

/**
 * Class InventoryInfo
 * @package OSS\Model
 */
class InventoryInfo
{

    private $id = "";
    private $isEnabled = "";
    private $destination = array();
    private $schedule = array();
    private $filter = "";
    private $includedObjectVersions = "";
    private $optionalFields = array();
	/**
	 * InventoryInfo constructor.
	 * @param string $id
	 * @param string $isEnabled
	 * @param array $destination
	 * @param array $schedule
	 * @param string $filter
	 * @param array $includedObjectVersions
	 * @param array $optionalFields
	 */
    public function __construct($id, $isEnabled, $destination,$schedule,$filter,$includedObjectVersions,$optionalFields)
    {
        $this->id = $id;
        $this->isEnabled = $isEnabled;
        $this->destination = $destination;
        $this->schedule = $schedule;
        $this->filter = $filter;
        $this->includedObjectVersions = $includedObjectVersions;
        $this->optionalFields = $optionalFields;
    }
    
    
    /**
     * @return mixed
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getIsEnabled(){
        return $this->isEnabled;
    }
    
    /**
     * @return array
     */
    public function getDestination(){
        return $this->destination;
    }
    
    /**
     * @return array
     */
    public function getOssBucketDestination(){
        return $this->destination['OSSBucketDestination'];
    }
    
    /**
     * @return array
     */
    public function getSchedule(){
        return $this->schedule;
    }
    
    /**
     * @return array
     */
    public function getFilter(){
        return $this->filter;
    }
    
    /**
     * @return string
     */
    public function getIncludedObjectVersions(){
        return $this->includedObjectVersions;
    }
    
    /**
     * @return array
     */
    public function getOptionalFields(){
        return $this->optionalFields;
    }
    
    
    
}