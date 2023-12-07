<?php

namespace OSS\Model;


/**
 * Class LifecycleNonCurrentVersionTransition
 * @package OSS\Model
 *
 */
class LifecycleNonCurrentVersionTransition
{

    /**
     * @var int
     */
    private $nonCurrentDays;
    /**
     * @var string
     */
    private $storageClass;
    /**
     * @var bool
     */
    private $isAccessTime;
    /**
     * @var bool
     */
    private $returnToStdWhenVisit;

    /**
     * @var bool
     */
    private $allowSmallFile;

    /**
     * LifecycleNonCurrentVersionTransition constructor
     * @param int $nonCurrentDays
     * @param string $storageClass
     * @param bool $isAccessTime
     * @param bool $returnToStdWhenVisit
     * @param bool $allowSmallFile
     */
    public function __construct($nonCurrentDays=null,$storageClass=null,$isAccessTime=null,$returnToStdWhenVisit=null,$allowSmallFile=null)
    {
        $this->nonCurrentDays = $nonCurrentDays;
        $this->storageClass = $storageClass;
        $this->isAccessTime = $isAccessTime;
        $this->returnToStdWhenVisit = $returnToStdWhenVisit;
        $this->allowSmallFile = $allowSmallFile;
    }
    /**
     * Get Days
     *
     * @return int
     */
    public function getNonCurrentDays()
    {
        return $this->nonCurrentDays;
    }

    /**
     * @param $nonCurrentDays int
     */
    public function setNonCurrentDays($nonCurrentDays)
    {
        $this->nonCurrentDays = $nonCurrentDays;
    }

    /**
     * Get storageClass
     * @return string
     */
    public function getStorageClass(){
        return $this->storageClass;
    }

    /**
     * Set storageClass
     * @param $storageClass string
     */
    public function setStorageClass($storageClass){
        $this->storageClass = $storageClass;
    }

    /**
     * Get isAccessTime
     * @return boolean
     */
    public function getIsAccessTime(){
        return $this->isAccessTime;
    }

    /**
     * Set isAccessTime
     * @param $isAccessTime boolean
     */
    public function setIsAccessTime($isAccessTime){
        $this->isAccessTime = $isAccessTime;
    }

    /**
     * Get Return To Std When Visit
     * @return boolean
     */
    public function getReturnToStdWhenVisit(){
        return $this->returnToStdWhenVisit;
    }

    /**
     * Set Return To Std When Visit
     * @param $returnToStdWhenVisit bool
     */
    public function setReturnToStdWhenVisit($returnToStdWhenVisit){
        $this->returnToStdWhenVisit = $returnToStdWhenVisit;
    }

    /**
     * Get Allow Small File
     * @return bool
     */
    public function getAllowSmallFile(){
        return $this->allowSmallFile;
    }

    /**
     * Set  Allow Small File
     * @param $allowSmallFile bool
     */
    public function setAllowSmallFile($allowSmallFile){
        $this->allowSmallFile = $allowSmallFile;
    }


    /**
     * @param \SimpleXMLElement $xmlRule
     */
    public function appendToXml(&$xmlRule)
    {
        if(isset($this->nonCurrentDays) || isset($this->storageClass) || isset($this->isAccessTime) || isset($this->returnToStdWhenVisit) || isset($this->allowSmallFile)){
            $xmlNonTransition = $xmlRule->addChild("NoncurrentVersionTransition");
        }

        if (isset($this->nonCurrentDays)){
            $xmlNonTransition->addChild('NoncurrentDays', $this->nonCurrentDays);
        }

        if (isset($this->storageClass)){
            $xmlNonTransition->addChild('StorageClass', $this->storageClass);
        }

        if (isset($this->isAccessTime)){
            $xmlNonTransition->addChild('IsAccessTime',is_bool($this->isAccessTime) ? json_encode($this->isAccessTime):$this->isAccessTime);
        }

        if (isset($this->returnToStdWhenVisit)){
            $xmlNonTransition->addChild('ReturnToStdWhenVisit', is_bool($this->returnToStdWhenVisit) ? json_encode($this->returnToStdWhenVisit):$this->returnToStdWhenVisit);
        }

        if (isset($this->allowSmallFile)){
            $xmlNonTransition->addChild('AllowSmallFile',is_bool($this->allowSmallFile) ? json_encode($this->allowSmallFile):$this->allowSmallFile);
        }

    }

}