<?php

namespace OSS\Model;


/**
 * Class LiveChannelConfig
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/44294.html
 */
class LiveChannelConfig implements XmlConfig
{
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $status;

    /**
     * @var LiveChannelConfigTarget
     */
    private $target;

    /**
     * @var LiveChannelConfigSnapshot
     */
    private $snapshot;


    /**
     * LiveChannelConfig constructor.
     * @param null|string $description
     * @param null|string $status
     * @param null|LiveChannelConfigTarget $target
     * @param null|LiveChannelConfigSnapshot $snapshot
     */
    public function __construct($description=null,$status=null,$target=null,$snapshot=null)
    {
            $this->description = $description;
            $this->status = $status;
            $this->target = $target;
            $this->snapshot = $snapshot;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function setDescription($description){
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status string
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param $target LiveChannelConfigTarget
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return LiveChannelConfigTarget
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $snapshot LiveChannelConfigSnapshot
     */
    public function setSnapshot($snapshot)
    {
        $this->snapshot = $snapshot;
    }

    /**
     * @return LiveChannelConfigSnapshot
     */
    public function getSnapshot()
    {
        return $this->snapshot;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Description) && !isset($xml->Status) && !isset($xml->Target) && !isset($xml->Snapshot)) return;
        if (isset($xml->Description)){
            $this->description = strval($xml->Description);
        }
        if (isset($xml->Status)){
            $this->status = strval($xml->Status);
        }
        if (isset($xml->Target)){
            $this->parseTarget($xml->Target);
        }
        if (isset($xml->Snapshot)){
            $this->parseSnapshot($xml->Snapshot);
        }
    }

    /**
     * @param $xmlTarget \SimpleXMLElement
     */
    private function parseTarget($xmlTarget){
        if (isset($xmlTarget)){
            $target = new LiveChannelConfigTarget();
            if (isset($xmlTarget->Type)){
                $target->setType(strval($xmlTarget->Type));
            }
            if (isset($xmlTarget->FragDuration)){
                $target->setFragDuration(strval($xmlTarget->FragDuration));
            }
            if (isset($xmlTarget->FragCount)){
                $target->setFragCount(strval($xmlTarget->FragCount));
            }
            if (isset($xmlTarget->PlayListName)){
                $target->setPlayListName(strval($xmlTarget->PlayListName));
            }

            $this->setTarget($target);
        }

    }

    /**
     * @param $xmlSnapshot \SimpleXMLElement
     */
    private function parseSnapshot($xmlSnapshot){
        if (isset($xmlSnapshot)){
            $snapshot = new LiveChannelConfigSnapshot();
            if (isset($xmlSnapshot->RoleName)){
                $snapshot->setRoleName(strval($xmlSnapshot->RoleName));
            }
            if (isset($xmlSnapshot->DestBucket)){
                $snapshot->setDestBucket(strval($xmlSnapshot->DestBucket));
            }
            if (isset($xmlSnapshot->NotifyTopic)){
                $snapshot->setNotifyTopic(strval($xmlSnapshot->NotifyTopic));
            }
            if (isset($xmlSnapshot->Interval)){
                $snapshot->setInterval(strval($xmlSnapshot->Interval));
            }
        }
    }

    public function serializeToXml()
    {
        $strXml = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<LiveChannelConfiguration>
</LiveChannelConfiguration>
EOF;
        $xml = new \SimpleXMLElement($strXml);
        if (isset($this->description)) {
            $xml->addChild('Description', $this->description);
        }else{
            $xml->addChild('Description');
        }
        if (isset($this->status)) {
            $xml->addChild('Status', $this->status);
        }
        if (isset($this->target)){
            $xmlTarget = $xml->addChild('Target');
            $this->target->appendToXml($xmlTarget);
        }

        if (isset($this->snapshot)){
            $xmlSnapshot = $xml->addChild('Snapshot');
            $this->snapshot->appendToXml($xmlSnapshot);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }


}
