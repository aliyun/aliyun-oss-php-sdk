<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
//*******************************简单使用***************************************************************

/**
 * 上传下载回调函数
 */
function progress_callback($consumed, $total)
{
    echo "consumed: ". $consumed . ", total: " . $total . "\n";
}

/**
 * 上传本地文件到oss
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$result = $ossClient->uploadFile($bucket, "oss_file", "local_file", $options);

/**
 * 上传字符串到oss
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$result = $ossClient->putObject($bucket, "oss_file", "this is a test for progress callback.", $options);

/**
 * 下载oss文件到本地
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
    OssClient::OSS_FILE_DOWNLOAD => "sample_progress_callback",
);
$result = $ossClient->getObject($bucket, "oss_file", $options);

/**
 * 下载oss文件到临时变量
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$content = $ossClient->getObject($bucket, "oss_file", $options);

/**
 * range get oss 到临时变量
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
    OssClient::OSS_RANGE => "0=>5",
);
$content = $ossClient->getObject($bucket, "oss_file", $options);

/**
 * 追加本地文件到oss
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$ossClient->appendFile($bucket, "oss_append_file0", "local_file", 0, $options);

/**
 * 追加临时变量到oss
 */
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$content_array = array('This is for test.');
$position = $ossClient->appendObject($bucket, "oss_append_object0", $content_array[0], 0, $options);

/**
 * 分片上传本地文件到oss
 */
exec('dd if=/dev/zero of=multipart_progress_sample bs=1M count=64 >/dev/null');
$options = array(
    OssClient::OSS_PROGRESS_CALLBACK => "progress_callback",
);
$result = $ossClient->multiuploadFile($bucket, "oss_file", "multipart_progress_sample", $options);
