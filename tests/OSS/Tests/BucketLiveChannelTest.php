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
        $this->bucketName = 'php-sdk-test-bucket-name-' . strval(rand(0, 10));
        $this->client->createBucket($this->bucketName);
        Common::waitMetaSync();
    }

    public function tearDown()
    {
    ////to delete created bucket
    //1. delele live channel
        $list = $this->client->listBucketLiveChannels($this->bucketName);
        if (count($list->getChannelList()) != 0)
        {
            foreach($list->getChannelList() as $list)
            {
                $this->client->deleteBucketLiveChannel($this->bucketName, $list->getName());
            }
        }
    //2. delete exsited object
        $prefix = 'live-test/';
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 1000;
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );

        try {
            $listObjectInfo = $this->client->listObjects($this->bucketName, $options);
        } catch (OssException $e) {
            printf($e->getMessage() . "\n");
            return;
        }

        $objectList = $listObjectInfo->getObjectList(); // 文件列表
        if (!empty($objectList))
        {   
            foreach($objectList as $objectInfo)
                $this->client->deleteObject($this->bucketName, $objectInfo->getKey());     
        }
    //3. delete the bucket
        $this->client->deleteBucket($this->bucketName);
    }

    public function testPutLiveChannel()
    {
        $config = new LiveChannelConfig(array(
            'name' => 'live-1',
            'description' => 'live channel 1',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $info = $this->client->putBucketLiveChannel($this->bucketName, $config);
        $this->client->deleteBucketLiveChannel($this->bucketName, 'live-1');

        $this->assertEquals('live-1', $info->getName());
        $this->assertEquals('live channel 1', $info->getDescription());
        $this->assertEquals(1, count($info->getPublishUrls()));
        $this->assertEquals(1, count($info->getPlayUrls()));
    }

    public function testListLiveChannels()
    {
       $config = new LiveChannelConfig(array(
            'name' => 'live-1',
            'description' => 'live channel 1',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $config = new LiveChannelConfig(array(
            'name' => 'live-2',
            'description' => 'live channel 2',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $list = $this->client->listBucketLiveChannels($this->bucketName);

        $this->assertEquals($this->bucketName, $list->getBucketName());
        $this->assertEquals(false, $list->getIsTruncated());
        $channels = $list->getChannelList();
        $this->assertEquals(2, count($channels));

        $chan1 = $channels[0];
        $this->assertEquals('live-1', $chan1->getName());
        $this->assertEquals('live channel 1', $chan1->getDescription());
        $this->assertEquals(1, count($chan1->getPublishUrls()));
        $this->assertEquals(1, count($chan1->getPlayUrls()));

        $chan2 = $channels[1];
        $this->assertEquals('live-2', $chan2->getName());
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
        $this->assertEquals('live-2', $chan2->getName());
        $this->assertEquals('live channel 2', $chan2->getDescription());
        $this->assertEquals(1, count($chan2->getPublishUrls()));
        $this->assertEquals(1, count($chan2->getPlayUrls()));

        $this->client->deleteBucketLiveChannel($this->bucketName, 'live-1');
        $this->client->deleteBucketLiveChannel($this->bucketName, 'live-2');
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => 'live-'
        ));
        $this->assertEquals(0, count($list->getChannelList()));
   }

    public function testDeleteLiveChannel()
    {
        $channelId = 'live-to-delete';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'live channel to delete',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        sleep(30);
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));

        $this->assertEquals(0, count($list->getChannelList()));
    }

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

        $this->assertTrue(isset($query['OSSAccessKeyId']));
        $this->assertTrue(isset($query['Signature']));
        $this->assertTrue(intval($query['Expires']) - ($now + 900) < 3);
        $this->assertEquals('hello', $query['a']);
        $this->assertEquals('world', $query['b']);
    }
    public function testLiveChannelInfo()
    {
        $channelId = 'live-to-put-status';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'test live channel info',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        //getLiveChannelInfo
        $info = $this->client->getLiveChannelInfo($this->bucketName, $channelId);
        $this->assertEquals('test live channel info', $info->getDescription());
        $this->assertEquals('enabled', $info->getStatus());
        $this->assertEquals('HLS', $info->getType());
        $this->assertEquals(10, $info->getFragDuration());
        $this->assertEquals(5, $info->getFragCount());
        $this->assertEquals('playlist.m3u8', $info->getPlayListName());

        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));
        $this->assertEquals(0, count($list->getChannelList()));
    }

    public function testPutLiveChannelStatus()
    {
        $channelId = 'live-to-put-status';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'test live channel info',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);
       
        $info = $this->client->getLiveChannelInfo($this->bucketName, $channelId);
        $this->assertEquals('test live channel info', $info->getDescription());
        $this->assertEquals('enabled', $info->getStatus());
        $this->assertEquals('HLS', $info->getType());
        $this->assertEquals(10, $info->getFragDuration());
        $this->assertEquals(5, $info->getFragCount());
        $this->assertEquals('playlist.m3u8', $info->getPlayListName());
        //$status = $this->client->getLiveChannelStatus($this->bucketName, $channelId);
        //$this->assertEquals('enabled', $status->getStatus());


        $resp = $this->client->putLiveChannelStatus($this->bucketName, $channelId, "disabled"); 
        $info = $this->client->getLiveChannelInfo($this->bucketName, $channelId);
        $this->assertEquals('test live channel info', $info->getDescription());
        $this->assertEquals('disabled', $info->getStatus());
        $this->assertEquals('HLS', $info->getType());
        $this->assertEquals(10, $info->getFragDuration());
        $this->assertEquals(5, $info->getFragCount());
        $this->assertEquals('playlist.m3u8', $info->getPlayListName());
        //$status = $this->client->getLiveChannelStatus($this->bucketName, $channelId);
        //$this->assertEquals('disabled', $status->getStatus());


        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));
        $this->assertEquals(0, count($list->getChannelList()));

    }
/****
    public function testGetLiveChannelStatus()
    {
        $channelId = 'live-1';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'live channel to delete',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);
       
        $status = $this->client->getLiveChannelStatus($this->bucketName, $channelId);
        $this->assertEquals('', $status->getStatus());
        $this->assertEquals('', $status->getConnectedTime());
        $this->assertEquals(672, $status->getVideoWidth());
        $this->assertEquals(378, $status->getVideoHeight());
        $this->assertEquals(29, $status->getVideoFrameRate());
        $this->assertEquals(72513, $status->getVideoBandwidth());
        $this->assertEquals('H264', $status->getVideoCodec());
        $this->assertEquals(6519, $status->getAudioBandwidth());
        $this->assertEquals(22050, $status->getAudioSampleRate());
        $this->assertEquals('AAC', $status->getAudioCodec());

        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));

        $this->assertEquals(0, count($list->getChannelList()));
    }
****/

/****    public function testLiveChannelHistory()
    {
        $channelId = 'live-test';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'test live channel info',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);
       
       $url = $this->client->getLiveChannelUrl($this->bucketName, $channelId, array(
            'expires' => 3600,
            'params' => array(
                'playlistName' => 'playlist.m3u8',
            )
        ));
        system("./ffmpeg \-re \-i ./allstar.flv \-c copy \-f flv '$url' ");
        sleep(2);
        system("./ffmpeg \-re \-i ./allstar.flv \-c copy \-f flv '$url' ");
        
        $history = $this->client->getLiveChannelHistory($this->bucketName, $channelId);
        $this->assertEquals(2, count($history->getLiveRecordList()));
        $this->assertNotEquals('', $history->getLiveRecordList()[0]->getStartTime());
        $this->assertNotEquals('', $history->getLiveRecordList()[0]->getEndTime());
        $this->assertNotEquals('', $history->getLiveRecordList()[0]->getRemoteAddr());
        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
    }
****/
/****    public function testPostVodPlayList()
    {
        $channelId = 'live-test';
        $config = new LiveChannelConfig(array(
            'name' => $channelId,
            'description' => 'live channel to delete',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
        $this->client->putBucketLiveChannel($this->bucketName, $config);

        $url = $this->client->getLiveChannelUrl($this->bucketName, $channelId, array(
            'expires' => 900,
            'params' => array(
                'playlistName' => 'playlist.m3u8',
            )
        ));

        system(" sudo ./ffmpeg \-re \-i ./allstar.flv \-c copy \-f flv '$url' ");
        sleep(1);
        
        $ts = time();
        $info = $this->client->postVodPlaylist($this->bucketName, $channelId, "playback.m3u8",
                                        array('StartTime' => $ts - 86400, 
                                        'EndTime' => $ts)
        );

        $this->client->deleteBucketLiveChannel($this->bucketName, $channelId);
        $list = $this->client->listBucketLiveChannels($this->bucketName, array(
            'prefix' => $channelId
        ));

        $this->assertEquals(0, count($list->getChannelList()));
    }
****/
}
