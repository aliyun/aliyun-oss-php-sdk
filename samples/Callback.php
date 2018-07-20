<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage ***************************************************************

/**  putObject Upload content to an OBS file using callback.
  * The callbackurl specifies the server url for the request callback.
  * The callbackbodytype can be application/json or application/x-www-form-urlencoded,the optional parameters,the default for the application/x - WWW - form - urlencoded
  * Users can choose not to set OBS_BACK_VAR
  */
$url =
    '{
        "callbackUrl":"callback.obs-demo.com:23450",
        "callbackHost":"obs-cn-hangzhou.aliyuncs.com",
        "callbackBody":"bucket=${bucket}&object=${object}&etag=${etag}&size=${size}&mimeType=${mimeType}&imageInfo.height=${imageInfo.height}&imageInfo.width=${imageInfo.width}&imageInfo.format=${imageInfo.format}&my_var1=${x:var1}&my_var2=${x:var2}",
         "callbackBodyType":"application/x-www-form-urlencoded"

    }';
$var = 
    '{
        "x:var1":"value1",
        "x:var2":"值2"
    }';
$options = array(ObsClient::OBS_CALLBACK => $url,
                 ObsClient::OBS_CALLBACK_VAR => $var
                );
$result = $obsClient->putObject($bucket, "b.file", "random content", $options);
Common::println($result['body']);
Common::println($result['info']['http_code']);

/**
  * completeMultipartUpload  Upload content to an OBS file using callback.
  * callbackurl specifies the server url for the request callback
  * The callbackbodytype can be application/json or application/x-www-form-urlencoded,the optional parameters,the default for the application/x - WWW - form - urlencoded
  * Users can choose not to set OBS_BACK_VAR.
 */
$object = "multipart-callback-test.txt";
$copiedObject = "multipart-callback-test.txt.copied";
$obsClient->putObject($bucket, $copiedObject, file_get_contents(__FILE__));

/**
  *  step 1. Initialize a block upload event, that is, a multipart upload process to get an upload id
  */
$upload_id = $obsClient->initiateMultipartUpload($bucket, $object);

/**
 * step 2. uploadPartCopy
 */
$copyId = 1;
$eTag = $obsClient->uploadPartCopy($bucket, $copiedObject, $bucket, $object, $copyId, $upload_id);
$upload_parts[] = array(
    'PartNumber' => $copyId,
    'ETag' => $eTag,
    );
$listPartsInfo = $obsClient->listParts($bucket, $object, $upload_id);

/**
 * step 3.
 */
$json = 
    '{
        "callbackUrl":"callback.obs-demo.com:23450",
        "callbackHost":"obs-cn-hangzhou.aliyuncs.com",
        "callbackBody":"{\"mimeType\":${mimeType},\"size\":${size},\"x:var1\":${x:var1},\"x:var2\":${x:var2}}",
        "callbackBodyType":"application/json"
    }';
$var = 
    '{
        "x:var1":"value1",
        "x:var2":"值2"
    }';
$options = array(ObsClient::OBS_CALLBACK => $json,
                 ObsClient::OBS_CALLBACK_VAR => $var);

$result = $obsClient->completeMultipartUpload($bucket, $object, $upload_id, $upload_parts, $options);
Common::println($result['body']);
Common::println($result['info']['http_code']);
