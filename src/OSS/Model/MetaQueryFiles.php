<?php

namespace OSS\Model;

/**
 * Class MetaQueryFiles
 * @package OSS\Model
 *
 */
class MetaQueryFiles
{
    /**
     * @var string
     */
    private $fileName;
    /**
     * @var int
     */
    private $size;
    /**
     * @var string
     */
    private $fileModifiedTime;
    /**
     * @var string
     */
    private $ossObjectType;
    /**
     * @var string
     */
    private $ossStorageClass;
    /**
     * @var string
     */
    private $objectAcl;

    /**
     * @var string
     */
    private $eTag;

    /**
     * @var string
     */
    private $ossCrc64;
    /**
     * @var int
     */
    private $ossTaggingCount;
    /**
     * @var MetaQueryUserMeta[]
     */
    private $ossUserMeta;
    /**
     * @var MetaQueryTagging[]
     */
    private $ossTagging;
    /**
     * @var string
     */
    private $serverSideEncryption;
    /**
     * @var string
     */
    private $serverSideEncryptionCustomerAlgorithm;

    /**
     * @return string
     */
    public function getFileName(){
        return $this->fileName;
    }

    /**
     * @param $fileName string
     */
    public function setFileName($fileName){
        $this->fileName = $fileName;
    }

    /**
     * @return int
     */
    public function getSize(){
        return $this->size;
    }

    /**
     * @param $size string
     */
    public function setSize($size){
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getFileModifiedTime(){
        return  $this->fileModifiedTime;
    }

    /**
     * @param $fileModifiedTime string
     */
    public function setFileModifiedTime($fileModifiedTime){
        $this->fileModifiedTime = $fileModifiedTime;
    }

    /**
     * @return string
     */
    public function getOssObjectType(){
        return $this->ossObjectType;
    }

    /**
     * @param $ossObjectType string
     */
    public function setOssObjectType($ossObjectType){
        $this->ossObjectType = $ossObjectType;
    }

    /**
     * @return string
     */
    public function getOssStorageClass(){
        return $this->ossStorageClass;
    }

    /**
     * @param $ossStorageClass string
     */
    public function setOssStorageClass($ossStorageClass){
        $this->ossStorageClass = $ossStorageClass;
    }

    /**
     * @return string
     */
    public function getObjectAcl(){
        return $this->objectAcl;
    }

    /**
     * @param $objectAcl string
     */
    public function setObjectAcl($objectAcl){
        $this->objectAcl = $objectAcl;
    }

    /**
     * @return string
     */
    public function getETag(){
        return $this->eTag;
    }

    /**
     * @param $eTag string
     */
    public function setETag($eTag){
        $this->eTag = $eTag;
    }

    /**
     * @return string
     */
    public function getOssCrc64(){
        return $this->ossCrc64;
    }

    /**
     * @param $ossCrc64 string
     */
    public function setOssCrc64($ossCrc64){
        $this->ossCrc64 = $ossCrc64;
    }

    /**
     * @return int
     */
    public function getOssTaggingCount(){
        return $this->ossTaggingCount;
    }

    /**
     * @param $ossTaggingCount string
     */
    public function setOssTaggingCount($ossTaggingCount){
        $this->ossTaggingCount = $ossTaggingCount;
    }

    /**
     * @return MetaQueryUserMeta[]
     */
    public function getOssUserMeta(){
        return $this->ossUserMeta;
    }

    /**
     * @param $ossUserMeta MetaQueryUserMeta
     */
    public function addOssUserMeta($ossUserMeta){
        $this->ossUserMeta[] = $ossUserMeta;
    }

    /**
     * @return MetaQueryTagging[]
     */
    public function getOssTagging(){
        return $this->ossTagging;
    }

    /**
     * @param $ossTagging MetaQueryTagging
     */
    public function addOssTagging($ossTagging){
        $this->ossTagging[] = $ossTagging;
    }

    /**
     * @return string
     */
    public function getServerSideEncryption(){
        return $this->serverSideEncryption;
    }

    /**
     * @param $serverSideEncryption string
     */
    public function setServerSideEncryption($serverSideEncryption){
        $this->serverSideEncryption= $serverSideEncryption;
    }

    /**
     * @return string
     */
    public function getServerSideEncryptionCustomerAlgorithm(){
        return $this->serverSideEncryptionCustomerAlgorithm;
    }

    /**
     * @param $serverSideEncryptionCustomerAlgorithm string
     */
    public function setServerSideEncryptionCustomerAlgorithm($serverSideEncryptionCustomerAlgorithm){
        $this->serverSideEncryptionCustomerAlgorithm= $serverSideEncryptionCustomerAlgorithm;
    }

    /**
     * @param \SimpleXMLElement $xmlFile
     */
    public function appendToXml(&$xmlFile)
    {
        if (isset($this->fileName)){
            $xmlFile->addChild('Filename', $this->fileName);
        }
        if (isset($this->size)){
            $xmlFile->addChild('Size', $this->size);
        }
        if (isset($this->fileModifiedTime)){
            $xmlFile->addChild('FileModifiedTime', $this->fileModifiedTime);
        }
        if (isset($this->ossObjectType)){
            $xmlFile->addChild('OSSObjectType', $this->ossObjectType);
        }
        if (isset($this->ossStorageClass)){
            $xmlFile->addChild('OSSStorageClass', $this->ossStorageClass);
        }
        if (isset($this->objectAcl)){
            $xmlFile->addChild('ObjectACL', $this->objectAcl);
        }

        if (isset($this->eTag)){
            $xmlFile->addChild('ETag', $this->eTag);
        }
        if (isset($this->ossCrc64)){
            $xmlFile->addChild('OSSCRC64', $this->ossCrc64);
        }
        if (isset($this->ossTaggingCount)){
            $xmlFile->addChild('OSSTaggingCount', $this->ossTaggingCount);
        }
        if (isset($this->ossTagging)){
            $xmlOssTagging = $xmlFile->addChild('OSSTagging');
            foreach ($this->ossTagging as $tagging){
                $tagging->appendToXml($xmlOssTagging);
            }
        }
        if (isset($this->ossUserMeta)){
            $xmlOssUserMeta = $xmlFile->addChild('OSSUserMeta');
            foreach ($this->ossUserMeta as $userMeta){
                $userMeta->appendToXml($xmlOssUserMeta);
            }
        }

    }
}