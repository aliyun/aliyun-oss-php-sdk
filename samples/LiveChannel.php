<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Model\LiveChannelConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* Simple Usage *******************************************************

/**
 *  Creates a Live Channel
 *  The live channel's name is test_rtmp_live. The play url file is test.m3u8, which has 3 ts file and each file is 5 seconds.（It's just for demo purpose, the actual length
 *  depends on the key frame.
 */
$config = new LiveChannelConfig(array(
            'description' => 'live channel test',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello.m3u8'
        ));
$info = $ossClient->putBucketLiveChannel($bucket, 'test_rtmp_live', $config);
Common::println("bucket $bucket liveChannel created:\n" . 
"live channel name: ". $info->getName() . "\n" .
"live channel description: ". $info->getDescription() . "\n" .
"publishurls: ". $info->getPublishUrls()[0] . "\n" .
"playurls: ". $info->getPlayUrls()[0] . "\n");

/**
  * list all existing live channels
  * prefix is the filter based on the live channel name's prefix.
  * max_keys means the max entries one list() call returns. Its max value is 1000. By default it's 100
 */
$list = $ossClient->listBucketLiveChannels($bucket);
Common::println("bucket $bucket listLiveChannel:\n" . 
"list live channel prefix: ". $list->getPrefix() . "\n" .
"list live channel marker: ". $list->getMarker() . "\n" .
"list live channel maxkey: ". $list->getMaxKeys() . "\n" .
"list live channel IsTruncated: ". $list->getIsTruncated() . "\n" .
"list live channel getNextMarker: ". $list->getNextMarker() . "\n");

foreach($list->getChannelList()  as $list)
{
    Common::println("bucket $bucket listLiveChannel:\n" . 
    "list live channel IsTruncated: ". $list->getName() . "\n" .
    "list live channel Description: ". $list->getDescription() . "\n" .
    "list live channel Status: ". $list->getStatus() . "\n" .
    "list live channel getNextMarker: ". $list->getLastModified() . "\n");
}
/**
  * Signs the RTMP url and publish url after the channel is created
 */
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600, array('params' => array('playlistName' => 'playlist.m3u8')));
Common::println("bucket $bucket rtmp url: \n" . $play_url);
$play_url = $ossClient->signRtmpUrl($bucket, "test_rtmp_live", 3600);
Common::println("bucket $bucket rtmp url: \n" . $play_url);

/**
  * If you want to disable a live channel (disable the pushing streaming), call putLiveChannelStatus with "Disabled" status.
  * Otherwise to enable a live channel, call PutLiveChannelStatus with "Enabled" status.
 */
$resp = $ossClient->putLiveChannelStatus($bucket, "test_rtmp_live", "enabled");

/**
  * Gets the Live channel information
 */
$info = $ossClient->getLiveChannelInfo($bucket, 'test_rtmp_live');
Common::println("bucket $bucket LiveChannelInfo:\n" . 
"live channel info description: ". $info->getDescription() . "\n" .
"live channel info status: ". $info->getStatus() . "\n" .
"live channel info type: ". $info->getType() . "\n" .
"live channel info fragDuration: ". $info->getFragDuration() . "\n" .
"live channel info fragCount: ". $info->getFragCount() . "\n" .
"live channel info playListName: ". $info->getPlayListName() . "\n");

/**
  * Gets the historical pushing streaming records by calling getLiveChannelHistory. Now the max records to return is 10.
 */
$history = $ossClient->getLiveChannelHistory($bucket, "test_rtmp_live");
if (count($history->getLiveRecordList()) != 0)
{
    foreach($history->getLiveRecordList() as $recordList)
    {
        Common::println("bucket $bucket liveChannelHistory:\n" . 
        "live channel history startTime: ". $recordList->getStartTime() . "\n" .
        "live channel history endTime: ". $recordList->getEndTime() . "\n" .
        "live channel history remoteAddr: ". $recordList->getRemoteAddr() . "\n");
    }
}

/**
  * Gets the live channel's status by calling getLiveChannelStatus.
  * If the live channel is receiving the pushing stream, all attributes in stat_result are valid.
  * If the live channel is idle or disabled, then the status is idle or Disabled and other attributes have no meaning.
 */
$status = $ossClient->getLiveChannelStatus($bucket, "test_rtmp_live");
Common::println("bucket $bucket listLiveChannel:\n" . 
"live channel status status: ". $status->getStatus() . "\n" .
"live channel status ConnectedTime: ". $status->getConnectedTime() . "\n" .
"live channel status VideoWidth: ". $status->getVideoWidth() . "\n" .
"live channel status VideoHeight: ". $status->getVideoHeight() . "\n" .
"live channel status VideoFrameRate: ". $status->getVideoFrameRate() . "\n" .
"live channel status VideoBandwidth: ". $status->getVideoBandwidth() . "\n" .
"live channel status VideoCodec: ". $status->getVideoCodec() . "\n" .
"live channel status AudioBandwidth: ". $status->getAudioBandwidth() . "\n" .
"live channel status AudioSampleRate: ". $status->getAudioSampleRate() . "\n" .
"live channel status AdioCodec: ". $status->getAudioCodec() . "\n");

/**
 *  If you want to generate a play url from the ts files generated from pushing streaming, call postVodPlayList.
 *  The start tiem is the current time minus 60s. The endtime is the current time.
 *  The playlist file is “vod_playlist.m3u8”. In other words, the vod_playlist.m3u8 is created after the all suceeded.
 */
$current_time = time();
$ossClient->postVodPlaylist($bucket,
    "test_rtmp_live", "vod_playlist.m3u8", 
    array('StartTime' => $current_time - 60, 
          'EndTime' => $current_time)
);

/**
 *  Deletes the live channel if the channel is not going to be used.
 */
$ossClient->deleteBucketLiveChannel($bucket, "test_rtmp_live");
