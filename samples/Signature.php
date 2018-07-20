<?php
require_once __DIR__ . '/Common.php';

use OBS\Http\RequestCore;
use OBS\Http\ResponseCore;
use OBS\ObsClient;
use OBS\Core\ObsException;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);

//******************************* Simple Usage ***************************************************************

$obsClient->uploadFile($bucket, "a.file", __FILE__);

// Generate a signed url for getting an object. The URL can be used in browser directly to download the file.
$signedUrl = $obsClient->signUrl($bucket, "a.file", 3600);
Common::println($signedUrl);

// Generate the signed url for putting an object. User can use put method with this url to upload a file to "a.file".
$signedUrl = $obsClient->signUrl($bucket, "a.file", "3600", "PUT");
Common::println($signedUrl);

// Generate the signed url for putting an object from local file. The url can be used directly to upload the file to "a.file".
$signedUrl = $obsClient->signUrl($bucket, "a.file", 3600, "PUT", array('Content-Type' => 'txt'));
Common::println($signedUrl);

//******************************* For complete usage, see the following functions ****************************************************

getSignedUrlForPuttingObject($obsClient, $bucket);
getSignedUrlForPuttingObjectFromFile($obsClient, $bucket);
getSignedUrlForGettingObject($obsClient, $bucket);

/**
 * Generate the signed url for getObject() to control read accesses under private privilege
 *
 * @param $obsClient ObsClient ObsClient instance
 * @param $bucket string bucket name
 * @return null
 */
function getSignedUrlForGettingObject($obsClient, $bucket)
{
    $object = "test/test-signature-test-upload-and-download.txt";
    $timeout = 3600;
    try {
        $signedUrl = $obsClient->signUrl($bucket, $object, $timeout);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");
    /**
     * Use similar code to access the object by url, or use browser to access the object.
     */
    $request = new RequestCore($signedUrl);
    $request->set_method('GET');
    $request->add_header('Content-Type', '');
    $request->send_request();
    $res = new ResponseCore($request->get_response_header(), $request->get_response_body(), $request->get_response_code());
    if ($res->isOK()) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    };
}

/**
 * Generate the signed url for PutObject to control write accesses under private privilege.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 * @throws ObsException
 */
function getSignedUrlForPuttingObject($obsClient, $bucket)
{
    $object = "test/test-signature-test-upload-and-download.txt";
    $timeout = 3600;
    $options = NULL;
    try {
        $signedUrl = $obsClient->signUrl($bucket, $object, $timeout, "PUT");
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");
    $content = file_get_contents(__FILE__);

    $request = new RequestCore($signedUrl);
    $request->set_method('PUT');
    $request->add_header('Content-Type', '');
    $request->add_header('Content-Length', strlen($content));
    $request->set_body($content);
    $request->send_request();
    $res = new ResponseCore($request->get_response_header(),
        $request->get_response_body(), $request->get_response_code());
    if ($res->isOK()) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    };
}

/**
 * Generate the signed url for PutObject's signed url. User could use the signed url to upload file directly.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @throws ObsException
 */
function getSignedUrlForPuttingObjectFromFile($obsClient, $bucket)
{
    $file = __FILE__;
    $object = "test/test-signature-test-upload-and-download.txt";
    $timeout = 3600;
    $options = array('Content-Type' => 'txt');
    try {
        $signedUrl = $obsClient->signUrl($bucket, $object, $timeout, "PUT", $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": signedUrl: " . $signedUrl . "\n");

    $request = new RequestCore($signedUrl);
    $request->set_method('PUT');
    $request->add_header('Content-Type', 'txt');
    $request->set_read_file($file);
    $request->set_read_stream_size(filesize($file));
    $request->send_request();
    $res = new ResponseCore($request->get_response_header(),
        $request->get_response_body(), $request->get_response_code());
    if ($res->isOK()) {
        print(__FUNCTION__ . ": OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FAILED" . "\n");
    };
}