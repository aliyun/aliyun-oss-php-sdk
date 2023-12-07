<?php

namespace OSS\Model;

/**
 * Class WebsiteIndexDocument
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31962.html
 */
class WebsiteIndexDocument {
    private $suffix;

    /**
     * @var boolean
     */
    private $supportSubDir;
    private $type;


    public function __construct($suffix=null,$supportSubDir=null,$type=null)
    {
        $this->suffix = $suffix;
        $this->supportSubDir = $supportSubDir;
        $this->type = $type;
    }

    /**
     * @param $suffix string
     */
    public function setSuffix($suffix){
        $this->suffix = $suffix;
    }


    /**
     * @return string
     */
    public function getSuffix(){
        return $this->suffix;
    }

    /**
     * @param $supportSubDir bool
     */
    public function setSupportSubDir($supportSubDir){
        $this->supportSubDir = $supportSubDir;
    }

    /**
     * @return boolean
     */
    public function getSupportSubDir(){
        return $this->supportSubDir;
    }

    /**
     * @param $type int
     */
    public function setType($type){
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @param \SimpleXMLElement $xmlIndexDocument
     */
    public function appendToXml(&$xmlIndexDocument)
    {
        if (isset($this->suffix)){
            $xmlIndexDocument->addChild('Suffix', $this->suffix);
        }
        if (isset($this->supportSubDir)){
            $xmlIndexDocument->addChild('SupportSubDir', json_encode($this->supportSubDir));
        }
        if (isset($this->type)){
            $xmlIndexDocument->addChild('Type', $this->type);
        }
    }

}