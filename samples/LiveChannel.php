<?php
require_once __DIR__ . '/Common.php';

use OSS\Http\RequestCore_Exception;
use OSS\OssClient;
use OSS\Model\LiveChannelConfig;
use OSS\Core\OssException;
use OSS\Model\LiveChannelConfigTarget;
use OSS\Model\LiveChannelConfigSnapshot;
$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

/**
 * Create a Live Channel
 * The live channel's name is test_rtmp_live.
 * The play url file is named as test.m3u8, which includes 3 ts files.
 * The time period of each file is 5 seconds.(It is recommneded value only for demo purpose, the actual period depends on the key frame.)
 *
 */
$target = new LiveChannelConfigTarget();

$target->setType(LiveChannelConfigTarget::HLS);
$target->setFragDuration(10);
$target->setFragCount(5);
$target->setPlayListName("hello.m3u8");
$description = "live channel test";
$status = "enabled";

$snapShot = new LiveChannelConfigSnapshot();
$snapShot->setNotifyTopic("example-topic");
$snapShot->setRoleName("example-role");
$snapShot->setDestBucket("demo-walker-6961");
$snapShot->setInterval(10);
$config = new LiveChannelConfig($description,$status,$target,$snapShot);

$info = $ossClient->putBucketLiveChannel($bucket, 'test_rtmp_live', $config);
$publicUrls = $info->getPublishUrls();
$playUrls = $info->getPlayUrls();
Common::println("bucket $bucket liveChannel created:\n" .
    "live channel name: ". $info->getName() . "\n" .
    "live channel description: ". $info->getDescription() . "\n" .
    "publish urls: ". $publicUrls[0] . "\n" .
    "play urls: ". $playUrls[0] . "\n");

/**
  * You can use listBucketLiveChannels to list and manage all existing live channels.
  * Prefix can be used to filter listed live channels by prefix.
  * Max_keys indicates the maximum numbers of live channels that can be listed in an iterator at one time. Its value is 1000 in maximum and 100 by default.
 */
$list = $ossClient->listBucketLiveChannels($bucket);
Common::println("bucket $bucket listLiveChannel:\n" .
    "list live channel prefix: ". $list->getPrefix() . "\n" .
    "list live channel marker: ". $list->getMarker() . "\n" .
    "list live channel max keys: ". $list->getMaxKeys() . "\n" .
    "list live channel Is Truncated: ". $list->getIsTruncated() . "\n" .
    "list live channel Next Marker: ". $list->getNextMarker() . "\n");

foreach($list->getChannelList() as $info)
{
    Common::println("bucket $bucket listLiveChannel:\n" .
        "list live channel Name: ". $info->getName() . "\n" .
        "list live channel Description: ". $info->getDescription() . "\n" .
        "list live channel Status: ". $info->getStatus() . "\n" .
        "list live channel Last Modified: ". $info->getLastModified() . "\n");
    $publicUrls = $info->getPublishUrls();
    $playUrls = $info->getPlayUrls();
    printf("list live channel Publish Urls: ". $publicUrls[0] . "\n" .
        "list live channel Play Urls: ".$playUrls[0] . "\n" );
}
/**
  * Obtain the play_url (url used for rtmp stream pushing.
  * If the the bucket is not globally readable and writable,
  * the url must be signed as shown in the following.) and pulish_url (the url included in the m3u8 file generated in stream pushing) used to push streams.
 */
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600, array('params' => array('playlistName' => 'playlist.m3u8')));
Common::println("bucket $bucket rtmp url: \n" . $play_url);
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600);
Common::println("bucket $bucket rtmp url: \n" . $play_url);

/**
  * If you want to disable a created live channel (disable the pushing streaming or do not allow stream pushing to an IP address), call putLiveChannelStatus to change the channel status to "Disabled".
  * If you want to enable a disabled live channel, call PutLiveChannelStatus to chanage the channel status to "Enabled".
 */
$resp = $ossClient->putLiveChannelStatus($bucket, "test_rtmp_live", "enabled");

/**
  * You can callLiveChannelInfo to get the information about a live channel.
 */
$info = $ossClient->getLiveChannelInfo($bucket, 'test_rtmp_live');
Common::println("bucket $bucket LiveChannelInfo:\n" . 
"live channel info description: ". $info->getDescription() . "\n" .
"live channel info status: ". $info->getStatus() . "\n" .
"live channel info type: ". $info->getType() . "\n" .
"live channel info frag Duration: ". $info->getFragDuration() . "\n" .
"live channel info frag Count: ". $info->getFragCount() . "\n" .
"live channel info play List Name: ". $info->getPlayListName() . "\n");

if ($info->getSnapshot() !== null){
    printf("live channel info Notify Topic: %s".PHP_EOL,$info->getSnapshot()->getNotifyTopic());
    printf("live channel info Dest Bucket: %s".PHP_EOL,$info->getSnapshot()->getDestBucket());
    printf("live channel info Interval: %s".PHP_EOL,$info->getSnapshot()->getInterval());
    printf("live channel info Role Name: %s".PHP_EOL,$info->getSnapshot()->getRoleName());
}

/**
  * Gets the historical pushing streaming records by calling getLiveChannelHistory. Now the max records to return is 10.
 */
$history = $ossClient->getLiveChannelHistory($bucket, "test_rtmp_live");
if ($history->getLiveRecordList())
{
    foreach($history->getLiveRecordList() as $recordList)
    {
        Common::println("bucket $bucket liveChannelHistory:\n" .
            "live channel history start Time: ". $recordList->getStartTime() . "\n" .
            "live channel history end Time: ". $recordList->getEndTime() . "\n" .
            "live channel history remote Address: ". $recordList->getRemoteAddr() . "\n");
    }
}

/**
  * Get the live channel's status by calling getLiveChannelStatus.
  * If the live channel is receiving the pushing stream, all attributes in stat_result are valid.
  * If the live channel is idle or disabled, then the status is idle or Disabled and other attributes have no meaning.
 */
$status = $ossClient->getLiveChannelStatus($bucket, "test_rtmp_live");
Common::println("bucket $bucket listLiveChannel:\n" . 
"live channel status status: ". $status->getStatus() . "\n" .
"live channel status Connected Time: ". $status->getConnectedTime() . "\n" .
"live channel status Video Width: ". $status->getVideoWidth() . "\n" .
"live channel status Video Height: ". $status->getVideoHeight() . "\n" .
"live channel status Video Frame Rate: ". $status->getVideoFrameRate() . "\n" .
"live channel status Video Band width: ". $status->getVideoBandwidth() . "\n" .
"live channel status Video Codec: ". $status->getVideoCodec() . "\n" .
"live channel status Audio Band width: ". $status->getAudioBandwidth() . "\n" .
"live channel status Audio Sample Rate: ". $status->getAudioSampleRate() . "\n" .
"live channel status Audio Codec: ". $status->getAudioCodec() . "\n");

/**
 * If you want to generate a play url from the ts files generated from pushing streaming, call postVodPlayList.
 * Specify the start time to 60 seconds before the current time and the end time to the current time, which means that a video of 60 seconds is generated.
 * The playlist file is specified to “vod_playlist.m3u8”, which means that a palylist file named vod_playlist.m3u8 is created after the interface is called.
 */
$current_time = time();
$ossClient->postVodPlaylist($bucket,
    "test_rtmp_live", "vod_playlist.m3u8", 
    array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
          OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
);
/**
 * Used to view the playlist generated by the specified LiveChannel streaming during the specified time period.
 */
$result = $ossClient->getVodPlaylist($bucket, "test_rtmp_live",
    array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
        OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
);

Common::println("bucket $bucket test_rtmp_live playlist:".$result.PHP_EOL);
/**
  *  Call delete_live_channel to delete a live channel if it will no longer be in used.
 */
$ossClient->deleteBucketLiveChannel($bucket, "test_rtmp_live");

//******************************* For complete usage, see the following functions  ****************************************************

putBucketLiveChannel($ossClient, $bucket);
listBucketLiveChannels($ossClient, $bucket);
signRtmpUrl($ossClient, $bucket);
deleteBucketLiveChannel($ossClient, $bucket);
putLiveChannelStatus($ossClient, $bucket);
getLiveChannelInfo($ossClient, $bucket);
getLiveChannelHistory($ossClient, $bucket);
postVodPlaylist($ossClient, $bucket);
getVodPlaylist($ossClient, $bucket);


/**
 * Create Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function putBucketLiveChannel($ossClient,$bucket){
    try {
        $target = new LiveChannelConfigTarget();
        $target->setType(LiveChannelConfigTarget::HLS);
        $target->setFragDuration(10);
        $target->setFragCount(5);
        $target->setPlayListName("hello.m3u8");
        $description = "live channel test";
        $status = "enabled";
        $config = new LiveChannelConfig($description,$status,$target);
        $info = $ossClient->putBucketLiveChannel($bucket, 'test_rtmp_live', $config);
        $publicUrls = $info->getPublishUrls();
        $playUrls = $info->getPlayUrls();
        printf("bucket $bucket liveChannel created:\n" .
            "live channel name: ". $info->getName() . "\n" .
            "live channel description: ". $info->getDescription() . "\n" .
            "publish urls: ". $publicUrls[0] . "\n" .
            "play urls: ". $playUrls[0] . "\n");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get List of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function listBucketLiveChannels($ossClient,$bucket){
    try {
        $list = $ossClient->listBucketLiveChannels($bucket);
        printf("bucket $bucket listLiveChannel:\n" .
            "list live channel prefix: ". $list->getPrefix() . "\n" .
            "list live channel marker: ". $list->getMarker() . "\n" .
            "list live channel max keys: ". $list->getMaxKeys() . "\n" .
            "list live channel Is Truncated: ". $list->getIsTruncated() . "\n" .
            "list live channel Next Marker: ". $list->getNextMarker() . "\n");

        foreach($list->getChannelList() as $info)
        {
            printf("bucket $bucket listLiveChannel:\n" .
                "list live channel name: ". $info->getName() . "\n" .
                "list live channel description: ". $info->getDescription() . "\n" .
                "list live channel status: ". $info->getStatus() . "\n" .
                "list live channel last modified: ". $info->getLastModified() . "\n");
            $publicUrls = $info->getPublishUrls();
            $playUrls = $info->getPlayUrls();
            printf("list live channel Publish Urls: ". $publicUrls[0] . "\n" .
                "list live channel Play Urls: ".$playUrls[0] . "\n" );
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Sign Rtmp Url Of Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 */
function signRtmpUrl($ossClient,$bucket){
    try {
        // Specify the name of the generated m3u8 file.
        $play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600, array('params' => array('playlistName' => 'playlist.m3u8')));
        printf("bucket $bucket rtmp url: %s \n" , $play_url);
        // Do not specify the m3u8 file name.
        //$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600);
        //printf("bucket $bucket rtmp url: %s \n" , $play_url);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Delete Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function deleteBucketLiveChannel($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $ossClient->deleteBucketLiveChannel($bucket,$channelName);
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Change Status Of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function putLiveChannelStatus($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $ossClient->putLiveChannelStatus($bucket,$channelName,"enabled");
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Info Of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getLiveChannelInfo($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $info = $ossClient->getLiveChannelInfo($bucket, $channelName);
        printf("bucket $bucket LiveChannelInfo:\n" .
            "live channel info description: ". $info->getDescription() . "\n" .
            "live channel info status: ". $info->getStatus() . "\n" .
            "live channel info type: ". $info->getType() . "\n" .
            "live channel info frag Duration: ". $info->getFragDuration() . "\n" .
            "live channel info frag Count: ". $info->getFragCount() . "\n" .
            "live channel info play List Name: ". $info->getPlayListName() . "\n");

        if ($info->getSnapshot() !== null){
            printf("live channel info Notify Topic: %s".PHP_EOL,$info->getSnapshot()->getNotifyTopic());
            printf("live channel info Dest Bucket: %s".PHP_EOL,$info->getSnapshot()->getDestBucket());
            printf("live channel info Interval: %s".PHP_EOL,$info->getSnapshot()->getInterval());
            printf("live channel info Role Name: %s".PHP_EOL,$info->getSnapshot()->getRoleName());
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get History Of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getLiveChannelHistory($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $history = $ossClient->getLiveChannelHistory($bucket, $channelName);
        if ($history->getLiveRecordList())
        {
            foreach($history->getLiveRecordList() as $recordList)
            {
                Common::println("bucket $bucket live Channel History:\n" .
                    "live channel history start Time: ". $recordList->getStartTime() . "\n" .
                    "live channel history end Time: ". $recordList->getEndTime() . "\n" .
                    "live channel history remote Address: ". $recordList->getRemoteAddr() . "\n");
            }
        }
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Create Vod Play list Of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function postVodPlaylist($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $playlistName = "vod_playlist.m3u8";
        $current_time = time();
        $ossClient->postVodPlaylist($bucket,
            $channelName, $playlistName,
            array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
                OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
        );
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get Vod Play list Of Bucket Live Channel
 *
 * @param $ossClient OssClient
 * @param  $bucket string bucket name
 * @return null
 * @throws RequestCore_Exception
 */
function getVodPlaylist($ossClient,$bucket){
    try {
        $channelName = "test_rtmp_live";
        $current_time = time();
        $ossClient->getVodPlaylist($bucket,
            $channelName,
            array(OssClient::OSS_LIVE_CHANNEL_START_TIME => $current_time - 60,
                OssClient::OSS_LIVE_CHANNEL_END_TIME => $current_time)
        );
    } catch (OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}




