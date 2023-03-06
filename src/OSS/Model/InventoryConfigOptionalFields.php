<?php

namespace OSS\Model;

use OSS\Core\OssException;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;


/**
 * Class InventoryConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfigOptionalFields
{

    /**
     * @var string|null
     */
    private $field;

    /**
     * InventoryConfigOptionalFields constructor.
     * @param null|string $field
     */
    public function __construct($field=null)
    {
        $this->field = $field;
    }

    /**
     * @return string|null
     */
    public function getFiled(){
        return $this->field;
    }

    /**
     * @param $xmlOptionalFields \SimpleXMLElement
     */
    public function appendToXml(&$xmlOptionalFields){
        if (isset($this->field)){
            $xmlOptionalFields->addChild("Field",$this->field);
        }
    }
    

}


