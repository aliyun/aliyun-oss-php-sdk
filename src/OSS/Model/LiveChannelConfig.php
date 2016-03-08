<?php

namespace OSS\Model;


use OSS\Core\OssException;

/**
 * Class LiveChannelConfig
 * @package OSS\Model
 *
 * TODO: fix link
 * @link http://help.aliyun.com/document_detail/oss/api-reference/cors/PutBucketcors.html
 */
class LiveChannelConfig implements XmlConfig
{
    private $id;
    private $description;
    private $status;
    private $type;
    private $fragDuration;
    private $playDuration;
    private $playListName;

    public function __construct($option = array())
    {
        if (isset($option['id'])) {
            $this->id = $option['id'];
        }
        if (isset($option['description'])) {
            $this->description = $option['description'];
        }
        if (isset($option['status'])) {
            $this->status = $option['status'];
        }
        if (isset($option['type'])) {
            $this->type = $option['type'];
        }
        if (isset($option['fragDuration'])) {
            $this->fragDuration = $option['fragDuration'];
        }
        if (isset($option['playDuration'])) {
            $this->playDuration = $option['playDuration'];
        }
        if (isset($option['playListName'])) {
            $this->playListName = $option['playListName'];
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFragDuration()
    {
        return $this->fragDuration;
    }

    public function getPlayDuration()
    {
        return $this->playDuration;
    }

    public function getPlayListName()
    {
        return $this->playListName;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        $this->description = strval($xml->Description);
        $this->status = strval($xml->Status);
        $target = $xml->Target;
        $this->type = strval($target->Type);
        $this->fragDuration = intval($target->FragDuration);
        $this->playDuration = intval($target->PlayDuration);
        $this->playListName = strval($target->PlayListName);
    }

    public function serializeToXml()
    {
        $strXml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<BucketLiveChannelConfiguration>
</BucketLiveChannelConfiguration>
EOF;
        $xml = new \SimpleXMLElement($strXml);
        if (isset($this->description)) {
            $xml->addChild('Description', $this->description);
        }

        if (isset($this->status)) {
            $xml->addChild('Status', $this->status);
        }

        $node = $xml->addChild('Target');
        $node->addChild('Type', $this->type);

        if (isset($this->fragDuration)) {
            $node->addChild('FragDuration', $this->fragDuration);
        }

        if (isset($this->playDuration)) {
            $node->addChild('PlayDuration', $this->playDuration);
        }

        if (isset($this->playListName)) {
            $node->addChild('PlayListName', $this->playListName);
        }

        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }
}
