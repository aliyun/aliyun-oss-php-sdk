<?php

namespace OSS\Model;

use OSS\Core\OssUtil;

/**
 * Class DoMetaQuery
 * @package OSS\Model
 */
class DoMetaQuery
{

    /**
     * @var string
     */
    private $nextToken;
    /**
     * @var MetaQueryFiles[]
     */
    private $files;
    /**
     * @var MetaQueryAggregation[]
     */
    private $aggregations;


    /**
     * @return string
     */
    public function getNextToken(){
        return $this->nextToken;
    }

    /**
     * @return MetaQueryFiles[]
     */
    public function getFiles(){
        return $this->files;
    }

    /**
     * @param $file MetaQueryFiles
     */
    public function addFiles($file){
        $this->files[] = $file;
    }

    /**
     * @return MetaQueryAggregation[]
     */
    public function getAggregations(){
        return $this->aggregations;
    }

    /**
     * @param $aggregation MetaQueryAggregation
     */
    public function addAggregations($aggregation){
        $this->aggregations[] = $aggregation;
    }

    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @return void|null
     */

    /**
     * Parse the xml into this object.
     * @param $strXml
     * @throws \OSS\Core\OssException
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->NextToken) && !isset($xml->Files) && !isset($xml->Aggregation)) return;
        if (isset($xml->NextToken)){
            $this->nextToken = strval($xml->NextToken);
        }
        if (isset($xml->Files)){
            $this->parseFiles($xml->Files);
        }
        if (isset($xml->Aggregations)){
            $this->parseAggregations($xml->Aggregations);
        }
    }

    /**
     * @param \SimpleXMLElement $xmlFiles
     */
    private function parseFiles($xmlFiles)
    {
        if (isset($xmlFiles)) {
            foreach ($xmlFiles->File as $file) {
                $metaQueryFiles = new MetaQueryFiles();
                if (isset($file->Filename)) {
                    $metaQueryFiles->setFileName(strval($file->Filename));
                }
                if (isset($file->Size)){
                    $metaQueryFiles->setSize(strval($file->Size));
                }
                if (isset($file->FileModifiedTime)){
                    $metaQueryFiles->setFileModifiedTime(strval($file->FileModifiedTime));
                }
                if (isset($file->OSSObjectType)){
                    $metaQueryFiles->setOssObjectType(strval($file->OSSObjectType));
                }
                if (isset($file->OSSStorageClass)){
                    $metaQueryFiles->setOssStorageClass(strval($file->OSSStorageClass));
                }
                if (isset($file->ObjectACL)){
                    $metaQueryFiles->setObjectAcl(strval($file->ObjectACL));
                }
                if (isset($file->ETag)){
                    $metaQueryFiles->setETag(strval($file->ETag));
                }
                if (isset($file->OSSCRC64)){
                    $metaQueryFiles->setOssCrc64(strval($file->OSSCRC64));
                }
                if (isset($file->OSSCRC64)){
                    $metaQueryFiles->setOssCrc64(strval($file->OSSCRC64));
                }
                if (isset($file->OSSTaggingCount)){
                    $metaQueryFiles->setOssTaggingCount(strval($file->OSSTaggingCount));
                }
                if (isset($file->OSSUserMeta)){
                    $this->parseOssUserMeta($file->OSSUserMeta,$metaQueryFiles);
                }
                if (isset($file->OSSTagging)){
                    $this->parseOssTagging($file->OSSTagging,$metaQueryFiles);
                }
                if (isset($file->ServerSideEncryption)){
                    $metaQueryFiles->setServerSideEncryption(strval($file->ServerSideEncryption));
                }
                if (isset($file->ServerSideEncryptionCustomerAlgorithm)){
                    $metaQueryFiles->setServerSideEncryptionCustomerAlgorithm(strval($file->ServerSideEncryptionCustomerAlgorithm));
                }
                $this->addFiles($metaQueryFiles);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $ossUserMeta
     * @param MetaQueryFiles $metaQueryFiles
     */
    private function parseOssUserMeta($ossUserMeta,&$metaQueryFiles)
    {
        if (isset($ossUserMeta->UserMeta)) {
            foreach ($ossUserMeta->UserMeta as $userMeta) {
                $queryUserMeta = new MetaQueryUserMeta();
                if (isset($userMeta->Key)){
                    $queryUserMeta->setKey(strval($userMeta->Key));
                }
                if (isset($userMeta->Value)){
                    $queryUserMeta->setValue(strval($userMeta->Value));
                }
                $metaQueryFiles->addOssUserMeta($queryUserMeta);
            }
        }
    }


    /**
     * @param \SimpleXMLElement $ossTagging
     * @param MetaQueryFiles $metaQueryFiles
     */
    private function parseOssTagging($ossTagging,&$metaQueryFiles)
    {
        if (isset($ossTagging->Tagging)) {
            foreach ($ossTagging->Tagging as $tag) {
                $metaQueryTag = new MetaQueryTagging();
                if (isset($tag->Key)){
                    $metaQueryTag->setKey(strval($tag->Key));
                }
                if (isset($tag->Value)){
                    $metaQueryTag->setValue(strval($tag->Value));
                }
                $metaQueryFiles->addOssTagging($metaQueryTag);
            }
        }
    }


    /**
     * @param $xml
     * @return array | MetaQueryAggregation[]
     */
    private function parseAggregations($xmlAggregations)
    {
        if (isset($xmlAggregations->Aggregation)) {
            foreach ($xmlAggregations->Aggregation as $aggregation) {
                $metaQueryAggregation = new MetaQueryAggregation();
                if (isset($aggregation->Field)){
                    $metaQueryAggregation->setField(strval($aggregation->Field));
                }
                if (isset($aggregation->Operation)){
                    $metaQueryAggregation->setOperation(strval($aggregation->Operation));
                }
                if (isset($aggregation->Value)){
                    $metaQueryAggregation->setValue(strval($aggregation->Value));
                }
                if (isset($aggregation->Groups)){
                    $this->parseGroups($aggregation->Groups,$metaQueryAggregation);
                }
                $this->addAggregations($metaQueryAggregation);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $xmlGroup
     * @param MetaQueryAggregation $metaQueryAggregation
     */
    private function parseGroups($xmlGroup,&$metaQueryAggregation){
        if (isset($xmlGroup->Group)) {
            foreach ($xmlGroup->Group as $group) {
                $metaQueryGroup = new MetaQueryGroup();
                if (isset($group->Value)){
                    $metaQueryGroup->setValue(strval($group->Value));
                }
                if (isset($group->Count)){
                    $metaQueryGroup->setCount(intval($group->Count));
                }
                $metaQueryAggregation->addGroup($metaQueryGroup);
            }
        }
    }

    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><MetaQuery></MetaQuery>');
        if (isset($this->nextToken)){
            $xml->addChild('NextToken',$this->nextToken);
        }
        if (isset($this->files)){
            $xmlFiles = $xml->addChild('Files');
            foreach ($this->files as $file) {
                $xmlFile = $xmlFiles->addChild('File');
                $file->appendToXml($xmlFile);
            }
        }

        if (isset($this->aggregations)){
            $xmlAggregations = $xml->addChild('Aggregations');
            foreach ($this->aggregations as $aggregation) {
                $xmlAggregation = $xmlAggregations->addChild('Aggregation');
                $aggregation->appendToXml($xmlAggregation);
            }
        }

        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }


}