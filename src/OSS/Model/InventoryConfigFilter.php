<?php

namespace OSS\Model;

/**
 * Class InventoryConfigFilter
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfigFilter
{
    /**
     * @var string|null
     */
    private $prefix;
    /**
     * @var string|null
     */
    private $lastModifyBeginTimeStamp;
    /**
     * @var string|null
     */
    private $lastModifyEndTimeStamp;
    /**
     * @var string|null
     */
    private $lowerSizeBound;
    /**
     * @var string|null
     */
    private $upperSizeBound;
    /**
     * @var string|null
     */
    private $storageClass;


    /**
     * InventoryConfigFilter constructor.
     * @param null $prefix
     * @param null $lastModifyBeginTimeStamp
     * @param null $lastModifyEndTimeStamp
     * @param null $lowerSizeBound
     * @param null $upperSizeBound
     * @param null $storageClass
     */
    public function __construct($prefix=null,$lastModifyBeginTimeStamp=null,$lastModifyEndTimeStamp=null,$lowerSizeBound=null,$upperSizeBound=null,$storageClass=null)
    {
        $this->prefix = $prefix;
        $this->lastModifyBeginTimeStamp = $lastModifyBeginTimeStamp;
        $this->lastModifyEndTimeStamp = $lastModifyEndTimeStamp;
        $this->lowerSizeBound = $lowerSizeBound;
        $this->upperSizeBound = $upperSizeBound;
        $this->storageClass = $storageClass;
    }

    /**
     * @return string|null
     */
    public function getPrefix(){
        return $this->prefix;
    }

    /**
     * @return string|null
     */
    public function getLastModifyBeginTimeStamp(){
        return $this->lastModifyBeginTimeStamp;
    }

    /**
     * @return string|null
     */
    public function getLastModifyEndTimeStamp(){
        return $this->lastModifyEndTimeStamp;
    }

    /**
     * @return string|null
     */
    public function getLowerSizeBound(){
        return $this->lowerSizeBound;
    }

    /**
     * @return string|null
     */
    public function getUpperSizeBound(){
        return $this->upperSizeBound;
    }

    /**
     * @return string|null
     */
    public function getStorageClass(){
        return $this->storageClass;
    }



    /**
     * @param $xmlFilter \SimpleXMLElement
     */
    public function appendToXml(&$xmlFilter){
        if ($this->prefix){
            $xmlFilter->addChild("Prefix",$this->prefix);
        }
        if ($this->lastModifyBeginTimeStamp){
            $xmlFilter->addChild("LastModifyBeginTimeStamp",$this->lastModifyBeginTimeStamp);
        }
        if ($this->lastModifyEndTimeStamp){
            $xmlFilter->addChild("LastModifyEndTimeStamp",$this->lastModifyEndTimeStamp);
        }
        if ($this->lowerSizeBound){
            $xmlFilter->addChild("LowerSizeBound",$this->lowerSizeBound);
        }
        if ($this->upperSizeBound){
            $xmlFilter->addChild("UpperSizeBound",$this->upperSizeBound);
        }
        if ($this->storageClass){
            $xmlFilter->addChild("StorageClass",$this->storageClass);
        }
    }
}


