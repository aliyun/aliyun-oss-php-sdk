<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$bucket = Common::getBucketName();
$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
//*******************************简单使用***************************************************************

$options = array(
    OssClient::OSS_FILE_DOWNLOAD => "example_download.jpg",
);

$serv = new swoole_http_server("127.0.0.1", 9503);

$serv->set(array(
	'worker_num' => 16,
	'daemonize' => true,
        'max_request' => 10000,
        'dispatch_mode' => 2,
        'debug_mode'=> 1,
	'log_file' => '/tmp/swoole_http_server.log',
));

$serv->on('Request', function($request, $response) use($ossClient, $bucket, $options){

	$ossClient->uploadFile($bucket, "example.jpg", "/tmp/example.jpg");
	$ossClient->getObject($bucket, "example.jpg", $options);

	$response->end("Hello Swoole\n");
});

$serv->start();
