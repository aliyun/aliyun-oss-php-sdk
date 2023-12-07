<?php

namespace OSS\Model;
use OSS\Core\OssException;
/**
 * Class LiveChannelConfigTarget
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/44294.html
 */
class LiveChannelConfigTarget
{
    const HLS = "HLS";
    /**
     * @var string enabled|disabled
     */
    private $type;
    /**
     * @var int
     */
    private $fragDuration;
    /**
     * @var string
     */
    private $fragCount;
    /**
     * @var string
     */
    private $playListName;

    /**
     * @param $type string
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $fragDuration int
     */
    public function setFragDuration($fragDuration)
    {
        $this->fragDuration = $fragDuration;
    }
    /**
     * @return string
     */
    public function getFragDuration()
    {
        return $this->fragDuration;
    }

    /**
     * @param $fragCount string
     */
    public function setFragCount($fragCount)
    {
        $this->fragCount = $fragCount;
    }
    /**
     * @return string
     */
    public function getFragCount()
    {
        return $this->fragCount;
    }


    /**
     * @param $playListName string
     */
    public function setPlayListName($playListName)
    {
        $this->playListName = $playListName;
    }

    /**
     * @return string
     */
    public function getPlayListName()
    {
        return $this->playListName;
    }

    /**
     * @param $xmlTarget \SimpleXMLElement
     */
    public function appendToXml($xmlTarget){

        if (isset($this->type)){
            $xmlTarget->addChild('Type', $this->type);
        }

        if (isset($this->fragDuration)) {
            $xmlTarget->addChild('FragDuration', $this->fragDuration);
        }

        if (isset($this->fragCount)) {
            $xmlTarget->addChild('FragCount', $this->fragCount);
        }

        if (isset($this->playListName)) {
            $xmlTarget->addChild('PlayListName', $this->playListName);
        }
    }

}
