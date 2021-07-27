<?php

namespace OSS\Model;

use OSS\Core\OssException;


/**
 * Class InventoryConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/177800.htm
 */
class InventoryConfig implements XmlConfig
{
    const OBJECT_VERSION_CURRENT = 'Current';
    const OBJECT_VERSION_ALL = 'All';

    const FREQUENCY_WEEKLY = 'Weekly';
    const FREQUENCY_DAILY = 'Daily';
    
    const IS_ENABIED_TRUE = 'true';
    const IS_ENABIED_FALSE = 'false';
    
    const FIELD_SIZE = 'Size';
    const FIELD_LAST_MODIFIED_DATE = 'LastModifiedDate';
    const FIELD_IS_MULTIPART_UPLOADED = 'IsMultipartUploaded';
    const FIELD_ETAG = 'ETag';
    const FIELD_STORAGECLASS = 'StorageClass';
    const FIELD_ENCRYPTIONSTATUS = 'EncryptionStatus';

    const DEST_FORMAT = 'CSV';
    
    private $config = array();
    /**
     * InventoryConfig constructor.
     * array(
        'Id'=>'report2',
        'IsEnabled'=>true,
        'Filter'=>array(
            'Prefix'=>'filterPrefix',
        ),
        'Destination'=> array(
            'OSSBucketDestination'=>array(
                'Format'=>'CSV',
                'AccountId'=>'1000000000000000',
                'RoleArn'=>'acs:ram::1000000000000000:role/AliyunOSSRole',
                'Bucket'=>'acs:oss:::<bucket_name>',
                'Prefix'=>'prefix1',
                'Encryption'=>array(
                    'SSE-KMS'=>array(
                        'KeyId'=>'key1'
                    )
                )
            ),
        ),
        'Schedule'=>array(
            'Frequency'=>'Daily',
        ),
        'IncludedObjectVersions'=>'All',
        'OptionalFields'=>array(
            'Field'=>array('Size','LastModifiedDate')
        )
    );
     */
    public function __construct()
    {
        $this->config = array();
    }

    /**
     * @param $configs array
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function addId($id)
    {
        $this->config['Id'] = $id;
    }

    public function addIsEnabled($isEnabled)
    {
        $this->config['IsEnabled'] = $isEnabled;
    }

    public function addDestination($destination)
    {
        $temp['OSSBucketDestination'] = $destination->oSSBucketDestination;
        $this->config['Destination'] = $temp;
    }

    public function addSchedule($schedule)
    {
        $this->config['Schedule']['Frequency'] = $schedule;
    }
    
    public function addPrefix($prefix)
    {
        $this->config['Filter']['Prefix'] = $prefix;
    }

    public function addIncludedObjectVersions($includedObjectVersions)
    {
        $this->config['IncludedObjectVersions'] = $includedObjectVersions;
    }


    /**
     * @param $fields array
     */
    public function addOptionalFields($fields)
    {
        $temp['Field'] = $fields;
        $this->config['OptionalFields'] = $temp;
    }



    /**
     * Parse the xml into this object.
     *
     * @param string $strXml
     * @throws OssException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $this->config = array();
        $xml = simplexml_load_string($strXml);
        $xmlJson= json_encode($xml);//将对象转换个JSON
        $this->config =json_decode($xmlJson,true);
    }


    /**
     * Serialize the object to xml
     *
     * @return string
     */
    public function serializeToXml()
    {
        return $xml = $this->arrToXml($this->config,null,null,'InventoryConfiguration');
    }

    /**
     * array turn to xml
     * @param $arr
     * @param $dom
     * @param $item
     * @param string $rootNode
     * @return string
     */
    public function arrToXml($arr,$dom,$item,$rootNode="Configuration")
    {
        if (!$dom) {
            $dom = new \DOMDocument("1.0",'utf-8');
        }
        if (!$item) {
            $item = $dom->createElement($rootNode);
            $dom->appendChild($item);
        }
        foreach ($arr as $key => $val) {
            if(!is_string($key)){
                continue;
            }
            if($key != 'Field'){
                $node = $dom->createElement($key);
                $item->appendChild($node);
            }else{
                foreach ($val as $value){
                    $node = $dom->createElement("Field",$value);
                    $item->appendChild($node);
                }
            }
            if (!is_array($val)) {
                if(is_bool($val)){
                    if($val == true){
                        $text = $dom->createTextNode('true');
                    }else{
                        $text = $dom->createTextNode('false');
                    }

                }else{
                    $text = $dom->createTextNode($val);
                }
                $node->appendChild($text);
            } else {
                $this->arrToXml($val, $dom, $node);
            }
        }
        return $dom->saveXML(null,LIBXML_NOEMPTYTAG);
    }

    /**
     *  Serialize the object into xml string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->serializeToXml();
    }
    
    /**
     * @return array
     */
    public function getConfig(){
        return $this->config;
    }

    public function getId(){
        return $this->config['Id'];
    }
    

}


