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
"list live channel count: ". count($list->getChannelList()) . "\n" .
"list live channel list: ". $list->getChannelList()[0]->getName() . "\n");

//getLiveChannelUrl
$url = $ossClient->getLiveChannelUrl($bucket, "live-1");
Common::println("bucket $bucket rtmp url: \n" . $url);

system(" sudo ffmpeg \-re \-i ./allstar.flv \-c copy \-f flv \"$url\" ");

//deleteLiveChannel
$info = $ossClient->deleteBucketLiveChannel($bucket, "live-1");
