<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;

$bucketName = Common::getBucketName();
$object = "example.jpg";
$obsClient = Common::getObsClient();
$download_file = "download.jpg";
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage ***************************************************************

// Upload example.jpg to the specified bucket and rename it to $object.
$obsClient->uploadFile($bucketName, $object, "example.jpg");

// Image resize
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/resize,m_fixed,h_100,w_100", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageResize",$download_file);

// Image crop
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/crop,w_100,h_100,x_100,y_100,r_1", );
$obsClient->getObject($bucketName, $object, $options);
printImage("iamgeCrop", $download_file);

// Image rotate
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/rotate,90", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageRotate", $download_file);

// Image sharpen
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/sharpen,100", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageSharpen", $download_file);

// Add watermark into a image
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/watermark,text_SGVsbG8g5Zu-54mH5pyN5YqhIQ", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageWatermark", $download_file);

// Image format convertion
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/format,png", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageFormat", $download_file);

// Get image information
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => $download_file,
    ObsClient::OBS_PROCESS => "image/info", );
$obsClient->getObject($bucketName, $object, $options);
printImage("imageInfo", $download_file);


/**
 * Generate a signed url which could be used in browser to access the object. The expiration time is 1 hour.
 */
 $timeout = 3600;
$options = array(
    ObsClient::OBS_PROCESS => "image/resize,m_lfit,h_100,w_100",
    );
$signedUrl = $obsClient->signUrl($bucketName, $object, $timeout, "GET", $options);
Common::println("rtmp url: \n" . $signedUrl);

// Finally delete the $object uploaded.
$obsClient->deleteObject($bucketName, $object);

function printImage($func, $imageFile)
{
    $array = getimagesize($imageFile);
    Common::println("$func, image width: " . $array[0]);
    Common::println("$func, image height: " . $array[1]);
    Common::println("$func, image type: " . ($array[2] === 2 ? 'jpg' : 'png'));
    Common::println("$func, image size: " . ceil(filesize($imageFile)));
}
