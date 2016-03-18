<?php

namespace OSS\Tests;

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Model\LiveChannelConfig;
use OSS\Model\LiveChannelInfo;
use OSS\Model\LiveChannelListInfo;
use OSS\Core\OssException;

class BucketLiveChannelTest extends \PHPUnit_Framework_TestCase
{
    private $bucketName;
    private $client;

    public function setUp()
    {
        $this->client = Common::getOssClient();
        $this->bucketName = 'php-sdk-test-bucket-' . strval(rand(0, 10));
        $this->client->createBucket($this->bucketName);
    }

    public function tearDown()
    {
        $this->client->deleteBucket($this->bucketName);
    }

    public function testPutLiveChannel()
    {
        $config = new LiveChannelConfig(array(
            'id' => 'live-1',
            'description' => 'live channel 1',
            'type' => 'hls',
            'fragDuration' => 1000,
            'playDuration' => 5000,
            'playListName' => 'hello'
        ));
        $info = $this->client->putBucketLiveChannel($this->bucketName, $config);

        $this->assertEquals('live-1', $info->getId());
        $this->assertEquals('live channel 1', $info->getDescription());
        $this->assertEquals(1, count($info->getPublishUrls()));
        $this->assertEquals(1, count($info->getPlayUrls()));
    }

    public function testListLiveChannels()
    {
        $config = new LiveChannelConfig(array(
            'id' => 'live-1',
            'description' => 'live channel 1',
            'type' => 'hls',
            'fragDuration' => 1000,
            'playDuration' => 5000,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $config = new LiveChannelConfig(array(
            'id' => 'live-2',
            'description' => 'live channel 2',
            'type' => 'hls',
            'fragDuration' => 1000,
            'playDuration' => 5000,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $list = $this->client->listBucketLiveChannels($this->bucketName);

        $this->assertEquals($this->bucketName, $list->getBucketName());
        $this->assertEquals(false, $list->getIsTruncated());
        $channels = $list->getChannelList();
        $this->assertEquals(2, count($channels));

        $chan1 = $channels[0];
        $this->assertEquals('live-1', $chan1->getId());
        $this->assertEquals('live channel 1', $chan1->getDescription());
        $this->assertEquals(1, count($chan1->getPublishUrls()));
        $this->assertEquals(1, count($chan1->getPlayUrls()));

        $chan2 = $channels[1];
        $this->assertEquals('live-2', $chan2->getId());
        $this->assertEquals('live channel 2', $chan2->getDescription());
        $this->assertEquals(1, count($chan2->getPublishUrls()));
        $this->assertEquals(1, count($chan2->getPlayUrls()));

        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => 'live-',
            'marker' => 'live-1',
            'max-keys' => 10
        ));
        $channels = $list->getChannelList();
        $this->assertEquals(1, count($channels));
        $chan2 = $channels[0];
        $this->assertEquals('live-2', $chan2->getId());
        $this->assertEquals('live channel 2', $chan2->getDescription());
        $this->assertEquals(1, count($chan2->getPublishUrls()));
        $this->assertEquals(1, count($chan2->getPlayUrls()));
    }

    /*
    public function testDeleteLiveChannel()
    {
        $channelId = 'live-to-delete';
        $config = new LiveChannelConfig(array(
            'id' => $channelId,
            'description' => 'live channel to delete',
            'type' => 'hls',
            'fragDuration' => 1000,
            'playDuration' => 5000,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $this->client->deleteBucketLiveChannel($channelId);
        $list = $this->listLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));

        $this->assertEquals(0, count($list->getChannelList()));
    }
    */

    public function testGetLiveChannelUrl()
    {
        $channelId = '90475';
        $bucket = 'douyu';
        $now = time();
        $url = $this->client->getLiveChannelUrl($bucket, $channelId, array(
            'expires' => 900,
            'params' => array(
                'a' => 'hello',
                'b' => 'world'
            )
        ));

        $ret = parse_url($url);
        $this->assertEquals('rtmp', $ret['scheme']);
        parse_str($ret['query'], $query);

        $this->assertTrue(isset($query['AccessKeyId']));
        $this->assertTrue(isset($query['Signature']));
        $this->assertTrue(intval($query['Expires']) - ($now + 900) < 3);
        $this->assertEquals('hello', $query['a']);
        $this->assertEquals('world', $query['b']);
    }
}
