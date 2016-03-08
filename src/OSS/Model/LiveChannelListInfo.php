<?php

namespace OSS\Model;

/**
 * Class LiveChannelListInfo
 *
 * ListBucketLiveChannels接口返回数据
 *
 * TODO fix link
 * @package OSS\Model
 * @link http://help.aliyun.com/document_detail/oss/api-reference/bucket/GetBucket.html
 */
class LiveChannelListInfo implements XmlConfig
{
    private $bucketName;
    private $prefix;
    private $marker;
    private $nextMarker;
    private $maxKeys;
    private $isTruncated;
    private $channelList = array();

    /**
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    public function setBucketName($name)
    {
        $this->bucketName = $name;
    }

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
     * @return LiveChannelInfo[]
     */
    public function getChannelList()
    {
        return $this->channelList;
    }

    /**
     * @return string
     */
    public function getNextMarker()
    {
        return $this->nextMarker;
    }

    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);

        $this->prefix = strval($xml->Prefix);
        $this->marker = strval($xml->Marker);
        $this->maxKeys = intval($xml->MaxKeys);
        $this->isTruncated = (strval($xml->IsTruncated) == 'true');
        $this->nextMarker = strval($xml->NextMarker);

        if (isset($xml->LiveChannel)) {
            foreach ($xml->LiveChannel as $chan) {
                $channel = new LiveChannelInfo();
                $channel->parseFromXmlNode($chan);
                $this->channelList[] = $channel;
            }
        }
    }

    public function serializeToXml()
    {
        throw new OssException("Not implemented.");
    }
}