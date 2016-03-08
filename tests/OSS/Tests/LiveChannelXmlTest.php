<?php

namespace OSS\Tests;


use OSS\Model\LiveChannelInfo;
use OSS\Model\LiveChannelListInfo;
use OSS\Model\LiveChannelConfig;
use OSS\Core\OssException;

class LiveChannelXmlTest extends \PHPUnit_Framework_TestCase
{
    private $config = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketLiveChannelConfiguration>
  <Description>xxx</Description>
  <Status>enabled</Status>
  <Target>
     <Type>hls</Type>
     <FragDuration>1000</FragDuration>
     <PlayDuration>5000</PlayDuration>
     <PlayListName>hello</PlayListName>
  </Target>
</BucketLiveChannelConfiguration>
BBBB;

    private $info = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCnameConfiguration>
  <Id>live-1</Id>
  <Description>xxx</Description>
  <PublishUrls>
    <Url>rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/213443245345</Url>
  </PublishUrls>
  <PlayUrls>
    <Url>http://bucket.oss-cn-hangzhou.aliyuncs.com/213443245345/播放列表.m3u8</Url>
  </PlayUrls>
  <Status>enabled</Status>
  <LastModified>2015-11-24T14:25:31.000Z</LastModified>
</BucketCnameConfiguration>
BBBB;

    private $list = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCnameConfiguration>
<Prefix>xxx</Prefix>
  <Marker>yyy</Marker>
  <MaxKeys>100</MaxKeys>
  <IsTruncated>false</IsTruncated>
  <NextMarker>121312132</NextMarker>
  <LiveChannel>
    <Id>12123214323431</Id>
    <Description>xxx</Description>
    <PublishUrls>
      <Url>rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/1</Url>
    </PublishUrls>
    <PlayUrls>
      <Url>http://bucket.oss-cn-hangzhou.aliyuncs.com/1/播放列表.m3u8</Url>
    </PlayUrls>
    <Status>enabled</Status>
    <LastModified>2015-11-24T14:25:31.000Z</LastModified>
  </LiveChannel>
  <LiveChannel>
    <Id>432423432423</Id>
    <Description>yyy</Description>
    <PublishUrls>
      <Url>rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/2</Url>
    </PublishUrls>
    <PlayUrls>
      <Url>http://bucket.oss-cn-hangzhou.aliyuncs.com/2/播放列表.m3u8</Url>
    </PlayUrls>
    <Status>enabled</Status>
    <LastModified>2016-11-24T14:25:31.000Z</LastModified>
  </LiveChannel>
</BucketCnameConfiguration>
BBBB;

    public function testConfig()
    {
        $config = new LiveChannelConfig(array('id' => 'live-1'));
        $config->parseFromXml($this->config);

        $this->assertEquals('live-1', $config->getId());
        $this->assertEquals('xxx', $config->getDescription());
        $this->assertEquals('enabled', $config->getStatus());
        $this->assertEquals('hls', $config->getType());
        $this->assertEquals(1000, $config->getFragDuration());
        $this->assertEquals(5000, $config->getPlayDuration());
        $this->assertEquals('hello', $config->getPlayListName());

        $xml = $config->serializeToXml();
        $config2 = new LiveChannelConfig(array('id' => 'live-2'));
        $config2->parseFromXml($xml);
        $this->assertEquals('live-2', $config2->getId());
        $this->assertEquals('xxx', $config2->getDescription());
        $this->assertEquals('enabled', $config2->getStatus());
        $this->assertEquals('hls', $config2->getType());
        $this->assertEquals(1000, $config2->getFragDuration());
        $this->assertEquals(5000, $config2->getPlayDuration());
        $this->assertEquals('hello', $config2->getPlayListName());
    }

    public function testInfo()
    {
        $info = new LiveChannelInfo();
        $info->parseFromXml($this->info);

        $this->assertEquals('live-1', $info->getId());
        $this->assertEquals('xxx', $info->getDescription());
        $this->assertEquals('enabled', $info->getStatus());
        $this->assertEquals('2015-11-24T14:25:31.000Z', $info->getLastModified());
        $pubs = $info->getPublishUrls();
        $this->assertEquals(1, count($pubs));
        $this->assertEquals('rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/213443245345', $pubs[0]);

        $plays = $info->getPlayUrls();
        $this->assertEquals(1, count($plays));
        $this->assertEquals('http://bucket.oss-cn-hangzhou.aliyuncs.com/213443245345/播放列表.m3u8', $plays[0]);
    }

    public function testList()
    {
        $list = new LiveChannelListInfo();
        $list->parseFromXml($this->list);

        $this->assertEquals('xxx', $list->getPrefix());
        $this->assertEquals('yyy', $list->getMarker());
        $this->assertEquals(100, $list->getMaxKeys());
        $this->assertEquals(false, $list->getIsTruncated());
        $this->assertEquals('121312132', $list->getNextMarker());

        $channels = $list->getChannelList();
        $this->assertEquals(2, count($channels));

        $chan1 = $channels[0];
        $this->assertEquals('12123214323431', $chan1->getId());
        $this->assertEquals('xxx', $chan1->getDescription());
        $this->assertEquals('enabled', $chan1->getStatus());
        $this->assertEquals('2015-11-24T14:25:31.000Z', $chan1->getLastModified());
        $pubs = $chan1->getPublishUrls();
        $this->assertEquals(1, count($pubs));
        $this->assertEquals('rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/1', $pubs[0]);

        $plays = $chan1->getPlayUrls();
        $this->assertEquals(1, count($plays));
        $this->assertEquals('http://bucket.oss-cn-hangzhou.aliyuncs.com/1/播放列表.m3u8', $plays[0]);

        $chan2 = $channels[1];
        $this->assertEquals('432423432423', $chan2->getId());
        $this->assertEquals('yyy', $chan2->getDescription());
        $this->assertEquals('enabled', $chan2->getStatus());
        $this->assertEquals('2016-11-24T14:25:31.000Z', $chan2->getLastModified());
        $pubs = $chan2->getPublishUrls();
        $this->assertEquals(1, count($pubs));
        $this->assertEquals('rtmp://bucket.oss-cn-hangzhou.aliyuncs.com/live/2', $pubs[0]);

        $plays = $chan2->getPlayUrls();
        $this->assertEquals(1, count($plays));
        $this->assertEquals('http://bucket.oss-cn-hangzhou.aliyuncs.com/2/播放列表.m3u8', $plays[0]);
    }
}
