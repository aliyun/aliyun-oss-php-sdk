<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class WebsiteMirrorHeaders
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/31970.html
 */
class WebsiteMirrorHeaders {

    /**
     * @var boolean
     */
    private $passAll;

    /**
     * @var array
     */
    private $pass;

    /**
     * @var array
     */
    private $remove;

    /**
     * @var WebsiteMirrorHeadersSet[]
     */
    private $set;

    const OSS_MAX = 10;

    /**
     * @param $passAll boolean
     */
    public function setPassAll($passAll){
        $this->passAll = $passAll;
    }

    /**
     * @param $pass string
     * @throws OssException
     */
    public function addPass($pass){
        if (isset($this->pass) && count($this->pass) >= self::OSS_MAX) {
            throw new OssException(
                "num of pass in the config exceeds : " . strval(self::OSS_MAX));
        }
        $this->pass[] = $pass;
    }

    /**
     * @param string $remove
     * @throws OssException
     */
    public function addRemove($remove){
        if (isset($this->remove) && count($this->remove) >= self::OSS_MAX) {
            throw new OssException(
                "num of remove in the config exceeds : " . strval(self::OSS_MAX));
        }
        $this->remove[] = $remove;
    }

    /**
     * @param $set WebsiteMirrorHeadersSet
     * @throws OssException
     */
    public function addSet($set){
        if (isset($this->set) &&  count($this->set) >= self::OSS_MAX) {
            throw new OssException(
                "num of set in the config exceeds : " . strval(self::OSS_MAX));
        }
        $this->set[] = $set;
    }

    /**
     * @return boolean
     */
    public function getPassAll(){
        return $this->passAll;
    }

    /**
     * @return array
     */
    public function getPass(){
        return $this->pass;
    }


    /**
     * @return array
     */
    public function getRemove(){
        return $this->remove;
    }


    /**
     * @return WebsiteMirrorHeadersSet[]
     */
    public function getSet(){
        return $this->set;
    }

    /**
     * @param \SimpleXMLElement $xmlMirrorHeaders
     */
    public function appendToXml(&$xmlMirrorHeaders)
    {
        if (isset($this->passAll)){
            $xmlMirrorHeaders->addChild('PassAll', json_encode($this->passAll));
        }
        if (isset($this->pass)){
            foreach ($this->pass as $pass){
                $xmlMirrorHeaders->addChild('Pass', $pass);
            }
        }
        if (isset($this->remove)){
            foreach ($this->remove as $remove){
                $xmlMirrorHeaders->addChild('Remove', $remove);
            }

        }
        if (isset($this->set)){
            foreach ($this->set as $set){
                $xmlSet = $xmlMirrorHeaders->addChild('Set');
                $set->appendToXml($xmlSet);
            }
        }
    }

}