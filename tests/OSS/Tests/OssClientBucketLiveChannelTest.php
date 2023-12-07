<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\LiveChannelConfig;
use OSS\Model\LiveChannelConfigTarget;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketLiveChannelTest extends TestOssClientBase
{
    public function testLiveChannel()
    {

        try {
            $target = new LiveChannelConfigTarget();

            $target->setType(LiveChannelConfigTarget::HLS);
            $target->setFragDuration(10);
            $target->setFragCount(5);
            $target->setPlayListName("hello.m3u8");
            $description = "live channel test";
            $status = "enabled";
            $config = new LiveChannelConfig($description,$status,$target);

            $result = $this->ossClient->putBucketLiveChannel($this->bucket,"test_rtmp_live",$config);
            var_dump($result);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $result = $this->ossClient->listBucketLiveChannels($this->bucket);
            var_dump($result);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $result = $this->ossClient->listBucketLiveChannels($this->bucket);
            var_dump($result);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $play_url = $this->ossClient->signRtmpUrl($this->bucket, "test_rtmp_live", 3600, array('params' => array('playlistName' => 'playlist.m3u8')));
            var_dump($play_url);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $resp = $this->ossClient->putLiveChannelStatus($this->bucket, "test_rtmp_live", "enabled");
            var_dump($resp);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $resp = $this->ossClient->putLiveChannelStatus($this->bucket, "test_rtmp_live", "enabled");
            var_dump($resp);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $resp = $this->ossClient->getLiveChannelInfo($this->bucket, "test_rtmp_live");
            var_dump($resp);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $resp = $this->ossClient->getLiveChannelHistory($this->bucket, "test_rtmp_live");
            var_dump($resp);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

        try {
            Common::waitMetaSync();
            $status = $this->ossClient->getLiveChannelStatus($this->bucket, "test_rtmp_live");
            var_dump($status);
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        $current_time = time();
        try {
            Common::waitMetaSync();
            $current_time = time();
            $this->ossClient->postVodPlaylist($this->bucket,
                "test_rtmp_live", "hello.m3u8",
                array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
                    OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
            );
        } catch (OssException $e) {
            $this->assertTrue(true);
        }

        try {
            Common::waitMetaSync();
            $result = $this->ossClient->getVodPlaylist($this->bucket,"test_rtmp_live",
                array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
                    OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
            );
            var_dump($result);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }


        try {
            Common::waitMetaSync();
            $this->ossClient->deleteBucketLiveChannel($this->bucket,"test_rtmp_live");
        } catch (OssException $e) {
            $this->assertTrue(false);
        }



    }


}
