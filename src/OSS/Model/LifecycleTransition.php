<?php

namespace OSS\Model;


/**
 * Class LifecycleTransition
 * @package OSS\Model
 *
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/PutBucketLifecycle.html
 */
class LifecycleTransition
{

    /**
     * @var int
     */
    private $days;

    /**
     * @var string
     */
    private $createdBeforeDate;

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
     * LifecycleTransition constructor
     * @param int $days
     * @param string $createdBeforeDate
     * @param string $storageClass
     * @param bool $isAccessTime
     * @param bool $returnToStdWhenVisit
     * @param bool $allowSmallFile
     */
    public function __construct($days=null,$createdBeforeDate=null,$storageClass=null,$isAccessTime=null,$returnToStdWhenVisit=null,$allowSmallFile=null)
    {
        $this->days = $days;
        $this->createdBeforeDate = $createdBeforeDate;
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
     * @return bool
     */
    public function getIsAccessTime(){
        return $this->isAccessTime;
    }

    /**
     * Set isAccessTime
     * @param $isAccessTime bool
     */
    public function setIsAccessTime($isAccessTime){
        $this->isAccessTime = $isAccessTime;
    }

    /**
     * Get Return To Std When Visit
     * @return bool
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
        if(isset($this->days) || isset($this->createdBeforeDate) || isset($this->storageClass) || isset($this->isAccessTime) || isset($this->returnToStdWhenVisit) || isset($this->allowSmallFile) ){
            $xmlTransition = $xmlRule->addChild("Transition");
        }

        if (isset($this->days)){
            $xmlTransition->addChild('Days', $this->days);
        }

        if (isset($this->createdBeforeDate)){
            $xmlTransition->addChild('CreatedBeforeDate', $this->createdBeforeDate);
        }

        if (isset($this->storageClass)){
            $xmlTransition->addChild('StorageClass', $this->storageClass);
        }

        if (isset($this->isAccessTime)){
            $xmlTransition->addChild('IsAccessTime', is_bool($this->isAccessTime) ? json_encode($this->isAccessTime):$this->isAccessTime);
        }

        if (isset($this->returnToStdWhenVisit)){
            $xmlTransition->addChild('ReturnToStdWhenVisit',is_bool($this->returnToStdWhenVisit) ? json_encode($this->returnToStdWhenVisit):$this->returnToStdWhenVisit);
        }

        if (isset($this->allowSmallFile)){
            $xmlTransition->addChild('AllowSmallFile',is_bool($this->allowSmallFile) ? json_encode($this->allowSmallFile):$this->allowSmallFile);
        }
    }
}