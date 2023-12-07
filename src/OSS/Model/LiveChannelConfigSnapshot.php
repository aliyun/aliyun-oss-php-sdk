<?php

namespace OSS\Model;

/**
 * Class LiveChannelConfigSnashot
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/44294.html
 */
class LiveChannelConfigSnapshot
{

    /**
     * @var string
     */
    private $roleName;
    /**
     * @var string
     */
    private $destBucket;
    /**
     * @var string
     */
    private $notifyTopic;
    /**
     * @var string
     */
    private $interval;

    /**
     * @return string
     */
    public function getRoleName(){
        return $this->roleName;
    }

    /**
     * @param $roleName string
     */
    public function setRoleName($roleName){
        $this->roleName = $roleName;
    }

    /**
     * @return string
     */
    public function getDestBucket(){
        return $this->destBucket;
    }

    /**
     * @param $destBucket string
     */
    public function setDestBucket($destBucket){
        $this->destBucket = $destBucket;
    }

    /**
     * @return string
     */
    public function getNotifyTopic(){
        return $this->notifyTopic;
    }

    /**
     * @param $notifyTopic string
     */
    public function setNotifyTopic($notifyTopic){
        $this->notifyTopic = $notifyTopic;
    }

    /**
     * @return string
     */
    public function getInterval(){
        return $this->interval;
    }

    /**
     * @param $interval string
     */
    public function setInterval($interval){
        $this->interval = $interval;
    }

    /**
     * @param $xmlSnapshot \SimpleXMLElement
     */
    public function appendToXml($xmlSnapshot){

        if (isset($this->roleName)){
            $xmlSnapshot->addChild('RoleName', $this->roleName);
        }

        if (isset($this->destBucket)) {
            $xmlSnapshot->addChild('DestBucket', $this->destBucket);
        }

        if (isset($this->notifyTopic)) {
            $xmlSnapshot->addChild('NotifyTopic', $this->notifyTopic);
        }

        if (isset($this->interval)) {
            $xmlSnapshot->addChild('Interval', $this->interval);
        }
    }


}
