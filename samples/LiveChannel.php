<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;
use OSS\Model\LiveChannelConfig;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);

//******************************* 简单使用 *******************************************************

//putLiveChannel
$config = new LiveChannelConfig(array(
            'name' => 'live-1',
            'description' => 'live channel 1',
            'type' => 'HLS',
            'fragDuration' => 10,
            'fragCount' => 5,
            'playListName' => 'hello'
        ));
$info = $ossClient->putBucketLiveChannel($bucket, $config);
Common::println("bucket $bucket liveChannel created:\n" . 
"live channel name: ". $info->getName() . "\n" .
"live channel description: ". $info->getDescription() . "\n" .
"publishurls: ". $info->getPublishUrls()[0] . "\n" .
"playurls: ". $info->getPlayUrls()[0] . "\n");

//listLiveChannel
$list = $ossClient->listBucketLiveChannels($bucket);
Common::println("bucket $bucket listLiveChannel:\n" . 
"list live channel prefix: ". $list->getPrefix() . "\n" .
"list live channel marker: ". $list->getMarker() . "\n" .
"list live channel maxkey: ". $list->getMaxKeys() . "\n" .
"list live channel IsTruncated: ". $list->getIsTruncated() . "\n" .
"list live channel getNextMarker: ". $list->getNextMarker() . "\n" .
"list live channel list: ". $list->getChannelList()[0]->getName() . "\n");

//getLiveChannelUrl
$url = $ossClient->getLiveChannelUrl($bucket, "live-1");
Common::println("bucket $bucket rtmp url: \n" . $url);

//putLiveChannelStatus
$resp = $ossClient->putLiveChannelStatus($bucket, "live-1", "enabled");

//getLiveChannelInfo
$info = $ossClient->getLiveChannelInfo($bucket, 'live-1');
Common::println("bucket $bucket listLiveChannel:\n" . 
"live channel info description: ". $info->getDescription() . "\n" .
"live channel info status: ". $info->getStatus() . "\n" .
"live channel info type: ". $info->getType() . "\n" .
"live channel info fragDuration: ". $info->getFragDuration() . "\n" .
"live channel info fragCount: ". $info->getFragCount() . "\n" .
"live channel info playListName: ". $info->getPlayListName() . "\n");

//getLiveChannelHistory
$history = $ossClient->getLiveChannelHistory($bucket, "live-1");
if (count($history->getLiveRecordList()) != 0)
{
    Common::println("bucket $bucket liveChannelHistory:\n" . 
    "live channel history startTime: ". $history->getLiveRecordList()[0]->getStartTime() . "\n" .
    "live channel history endTime: ". $history->getLiveRecordList()[0]->getEndTime() . "\n" .
    "live channel history remoteAddr: ". $history->getLiveRecordList()[0]->getRemoteAddr() . "\n");
}

//getLiveChannelStatus
$status = $ossClient->getLiveChannelStatus($bucket, "live-1");
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

//postVodPlaylist
$info = $ossClient->postVodPlaylist($bucket,
 "live-1", "playback.m3u8", 
 array('StartTime' => '1476844172', 
 'EndTime' => '1476864172')
);

//deleteLiveChannel
$info = $ossClient->deleteBucketLiveChannel($bucket, "live-1");
