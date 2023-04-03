<?php

namespace OSS\Model;
use OSS\Core\OssException;

/**
 * Class GetLiveChannelInfo
 * @package OSS\Model
 */
class GetLiveChannelInfo implements XmlConfig
{
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
   
    public function getFragCount()
    {
        return $this->fragCount;
    }
   
    public function getPlayListName()
    {
        return $this->playlistName;
    }

    public function getSnapshot(){
        return $this->snapshot;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);

        $this->description = strval($xml->Description);
        $this->status = strval($xml->Status);

        if (isset($xml->Target)) {
            $target = $xml->Target;
            $this->type = strval($target->Type);
            $this->fragDuration = strval($target->FragDuration);
            $this->fragCount = strval($target->FragCount);
            $this->playlistName = strval($target->PlaylistName);
        }

        if (isset($xml->Snapshot)) {
            $snapshot = $xml->Snapshot;

            $snap = new LiveChannelConfigSnapshot();

            if (isset($snapshot->RoleName)){
                $snap->setRoleName(strval($snapshot->RoleName));
            }

            if (isset($snapshot->Interval)){
                $snap->setInterval(strval($snapshot->Interval));
            }

            if (isset($snapshot->DestBucket)){
                $snap->setDestBucket(strval($snapshot->DestBucket));
            }

            if (isset($snapshot->NotifyTopic)){
                $snap->setNotifyTopic(strval($snapshot->NotifyTopic));
            }

            $this->snapshot = $snap;
        }
    }

    public function serializeToXml()
    {
        throw new OssException("Not implemented.");
    }
    
    private $description;
    private $status;
    private $type;
    private $fragDuration;
    private $fragCount;
    private $playlistName;
    /**
     * @var LiveChannelConfigSnapshot|null
     */
    private $snapshot;
}
