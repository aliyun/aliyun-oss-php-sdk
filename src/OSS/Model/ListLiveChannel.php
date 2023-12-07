<?php

namespace OSS\Model;

use OSS\Core\OssException;

/**
 * Class ListLiveChannel
 *
 * The data returned by ListBucketLiveChannels
 *
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucket.html
 */
class ListLiveChannel
{
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string
     */
    private $marker;
    /**
     * @var string
     */
    private $nextMarker;
    /**
     * @var string
     */
    private $maxKeys;
    /**
     * @var string
     */
    private $isTruncated;
    /**
     * @var LiveChannelInfo[]
     */
    private $liveChannel;

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @return int
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }

    /**
     * @return mixed
     */
    public function getIsTruncated()
    {
        return $this->isTruncated;
    }

    /**
     * @return string
     */
    public function getNextMarker()
    {
        return $this->nextMarker;
    }

    /**
     * @return LiveChannelInfo[]
     */
    public function getChannelList(){
        return $this->liveChannel;
    }

    /**
     * @param $channel LiveChannelInfo
     */
    public function addLiveChannel($channel){
        $this->liveChannel[] = $channel;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->Prefix) && !isset($xml->Marker) && !isset($xml->MaxKeys)  && !isset($xml->IsTruncated)  && !isset($xml->NextMarker) && !isset($xml->LiveChannel)) return;
        if (isset($xml->Prefix)){
            $this->prefix = strval($xml->Prefix);
        }
        if (isset($xml->Marker)){
            $this->marker = strval($xml->Marker);
        }
        if (isset($xml->MaxKeys)){
            $this->maxKeys = strval($xml->MaxKeys);
        }
        if (isset($xml->IsTruncated)){
            $this->isTruncated = strval($xml->IsTruncated);
        }
        if (isset($xml->NextMarker)){
            $this->nextMarker = strval($xml->NextMarker);
        }
        if (isset($xml->LiveChannel)){
            $this->parseLiveChannel($xml->LiveChannel);
        }

    }

    /**
     * @param $xmlLiveChannel \SimpleXMLElement
     */
    private function parseLiveChannel($xmlLiveChannel){
        if (isset($xmlLiveChannel)){
            foreach ($xmlLiveChannel as $liveChannel){
                $liveChannelInfo = new LiveChannelInfo();
                if (isset($liveChannel->Name)){
                    $liveChannelInfo->setName(strval($liveChannel->Name));
                }
                if (isset($liveChannel->Description)){
                    $liveChannelInfo->setDescription(strval($liveChannel->Description));
                }
                if (isset($liveChannel->Status)){
                    $liveChannelInfo->setStatus(strval($liveChannel->Status));
                }
                if (isset($liveChannel->LastModified)){
                    $liveChannelInfo->setLastModified(strval($liveChannel->LastModified));
                }
                if (isset($liveChannel->PublishUrls)) {
                    foreach ($liveChannel->PublishUrls as $url) {
                        $liveChannelInfo->addPublishUrls(strval($url->Url));
                    }
                }
                if (isset($liveChannel->PlayUrls)) {
                    foreach ($liveChannel->PlayUrls as $url) {
                        $liveChannelInfo->addPlayUrls(strval($url->Url));
                    }
                }
                $this->addLiveChannel($liveChannelInfo);
            }

        }
    }
}
