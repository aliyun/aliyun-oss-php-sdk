<?php
require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Core\OssException;

$ossClient = Common::getOssClient();
if (is_null($ossClient)) exit(1);
$bucket = Common::getBucketName();

//******************************* Simple Usage****************************************************************

// show all object list
$option = array(
	OssClient::OSS_KEY_MARKER => null,
	OssClient::OSS_VERSION_ID_MARKER => null
);
$bool = true;
while ($bool){
	$result = $ossClient->listObjectVersions($bucket,$option);
	## 查看列举Object的版本信息。
	foreach ($result->getObjectVersionList() as $key => $info){
		Common::println("key name: ".$info->getKey());
		Common::println("versionid: ".$info->getVersionId());
		Common::println("Is latest: ".$info->getIsLatest());
	}
	
	## 查看列举删除标记的版本信息。
	foreach ($result->getDeleteMarkerList() as $key => $info){
		Common::println("del_maker key name: ".$info->getKey());
		Common::println("del_maker versionid: ".$info->getVersionId());
		Common::println("del_maker Is latest: ".$info->getIsLatest());
	}
	
	if($result->getIsTruncated() === 'true'){
		$option = array(
			OssClient::OSS_KEY_MARKER => $result->getNextKeyMarker(),
			OssClient::OSS_VERSION_ID_MARKER => $result->getNextVersionIdMarker()
		);
	}else{
		$bool = false;
	}
}

// show the prefix object

$option = array(
	OssClient::OSS_KEY_MARKER => null,
	OssClient::OSS_VERSION_ID_MARKER => null,
	OssClient::OSS_PREFIX => "test"
);
$bool = true;
while ($bool){
	$result = $ossClient->listObjectVersions($bucket,$option);
	## 查看列举Object的版本信息。
	foreach ($result->getObjectVersionList() as $key => $info){
		Common::println("key name: ".$info->getKey());
		Common::println("versionid: ".$info->getVersionId());
		Common::println("Is latest: ".$info->getIsLatest());
	}
	
	## 查看列举删除标记的版本信息。
	foreach ($result->getDeleteMarkerList() as $key => $info){
		Common::println("del_maker key name: ".$info->getKey());
		Common::println("del_maker versionid: ".$info->getVersionId());
		Common::println("del_maker Is latest: ".$info->getIsLatest());
	}
	
	if($result->getIsTruncated() === 'true'){
		$option[OssClient::OSS_KEY_MARKER] = $result->getNextKeyMarker();
		$option[OssClient::OSS_VERSION_ID_MARKER] = $result->getNextVersionIdMarker();
	}else{
		$bool = false;
	}
}

// list the number of objects

$option = array(
	OssClient::OSS_KEY_MARKER => null,
	OssClient::OSS_VERSION_ID_MARKER => null,
	OssClient::OSS_MAX_KEYS => 200
);

$result = $ossClient->listObjectVersions($bucket,$option);
## 查看列举Object的版本信息。
foreach ($result->getObjectVersionList() as $key => $info){
	Common::println("key name: ".$info->getKey());
	Common::println("versionid: ".$info->getVersionId());
	Common::println("Is latest: ".$info->getIsLatest());
}

## 查看列举删除标记的版本信息。
foreach ($result->getDeleteMarkerList() as $key => $info){
	Common::println("del_maker key name: ".$info->getKey());
	Common::println("del_maker versionid: ".$info->getVersionId());
	Common::println("del_maker Is latest: ".$info->getIsLatest());
}


// show root folder list
$option = array(
	OssClient::OSS_KEY_MARKER => null,
	OssClient::OSS_VERSION_ID_MARKER => null,
	OssClient::OSS_DELIMITER => "/",
);
$bool = true;
while ($bool){
	$result = $ossClient->listObjectVersions($bucket,$option);
	## 查看列举Object的版本信息。
	foreach ($result->getObjectVersionList() as $key => $info){
		Common::println("key name: ".$info->getKey());
		Common::println("versionid: ".$info->getVersionId());
		Common::println("Is latest: ".$info->getIsLatest());
	}
	
	## 查看列举删除标记的版本信息。
	foreach ($result->getDeleteMarkerList() as $key => $info){
		Common::println("del_maker key name: ".$info->getKey());
		Common::println("del_maker versionid: ".$info->getVersionId());
		Common::println("del_maker Is latest: ".$info->getIsLatest());
	}
	
	if($result->getIsTruncated() === 'true'){
		$option[OssClient::OSS_KEY_MARKER] = $result->getNextKeyMarker();
		$option[OssClient::OSS_VERSION_ID_MARKER] = $result->getNextVersionIdMarker();
	}else{
		$bool = false;
	}
}

//  Show subfolder objects list
$option = array(
	OssClient::OSS_KEY_MARKER => null,
	OssClient::OSS_VERSION_ID_MARKER => null,
	OssClient::OSS_DELIMITER => "/",
	OssClient::OSS_PREFIX => "test/",
);
$bool = true;
while ($bool){
	$result = $ossClient->listObjectVersions($bucket,$option);
	## 查看列举Object的版本信息。
	foreach ($result->getObjectVersionList() as $key => $info){
		Common::println("key name: ".$info->getKey());
		Common::println("versionid: ".$info->getVersionId());
		Common::println("Is latest: ".$info->getIsLatest());
	}
	
	## 查看列举删除标记的版本信息。
	foreach ($result->getDeleteMarkerList() as $key => $info){
		Common::println("del_maker key name: ".$info->getKey());
		Common::println("del_maker versionid: ".$info->getVersionId());
		Common::println("del_maker Is latest: ".$info->getIsLatest());
	}
	
	if($result->getIsTruncated() === 'true'){
		$option[OssClient::OSS_KEY_MARKER] = $result->getNextKeyMarker();
		$option[OssClient::OSS_VERSION_ID_MARKER] = $result->getNextVersionIdMarker();
	}else{
		$bool = false;
	}
}


//******************************* For complete usage, see the following functions ****************************************************

listObjectVersions($ossClient, $bucket);

/**
 * @param OssClient $ossClient OssClient instance
 * @param string $bucket Name of the bucket to create
 * @return null
 */
function listObjectVersions($ossClient, $bucket)
{
	try {
		$option = array(
			OssClient::OSS_KEY_MARKER => null,
			OssClient::OSS_VERSION_ID_MARKER => null,
		);
		$bool = true;
		while ($bool){
			$result = $ossClient->listObjectVersions($bucket,$option);
			## 查看列举Object的版本信息。
			foreach ($result->getObjectVersionList() as $key => $info){
				Common::println("key name: ".$info->getKey());
				Common::println("versionid: ".$info->getVersionId());
				Common::println("Is latest: ".$info->getIsLatest());
			}
			
			## 查看列举删除标记的版本信息。
			foreach ($result->getDeleteMarkerList() as $key => $info){
				Common::println("del_maker key name: ".$info->getKey());
				Common::println("del_maker versionid: ".$info->getVersionId());
				Common::println("del_maker Is latest: ".$info->getIsLatest());
			}
			
			if($result->getIsTruncated() === 'true'){
				$option[OssClient::OSS_KEY_MARKER] = $result->getNextKeyMarker();
				$option[OssClient::OSS_VERSION_ID_MARKER] = $result->getNextVersionIdMarker();
			}else{
				$bool = false;
			}
		}
	} catch (OssException $e) {
		printf(__FUNCTION__ . ": FAILED\n");
		printf($e->getMessage() . "\n");
		return;
	}
	print(__FUNCTION__ . ": OK" . "\n");
}