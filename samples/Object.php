<?php
require_once __DIR__ . '/Common.php';

use OBS\ObsClient;
use OBS\Core\ObsException;

$bucket = Common::getBucketName();
$obsClient = Common::getObsClient();
if (is_null($obsClient)) exit(1);
//******************************* Simple usage ***************************************************************

// Upload the in-memory string (hi, obs) to an OBS file
$result = $obsClient->putObject($bucket, "b.file", "hi, obs");
Common::println("b.file is created");
Common::println($result['x-obs-request-id']);
Common::println($result['etag']);
Common::println($result['content-md5']);
Common::println($result['body']);

// Uploads a local file to an OBS file
$result = $obsClient->uploadFile($bucket, "c.file", __FILE__);
Common::println("c.file is created");
Common::println("b.file is created");
Common::println($result['x-obs-request-id']);
Common::println($result['etag']);
Common::println($result['content-md5']);
Common::println($result['body']);

// Download an obs object as an in-memory variable
$content = $obsClient->getObject($bucket, "b.file");
Common::println("b.file is fetched, the content is: " . $content);

// Add a symlink to an object
$content = $obsClient->putSymlink($bucket, "test-symlink", "b.file");
Common::println("test-symlink is created");
Common::println($result['x-obs-request-id']);
Common::println($result['etag']);

// Get a symlink
$content = $obsClient->getSymlink($bucket, "test-symlink");
Common::println("test-symlink refer to : " . $content[ObsClient::OBS_SYMLINK_TARGET]);

// Download an object to a local file.
$options = array(
    ObsClient::OBS_FILE_DOWNLOAD => "./c.file.localcopy",
);
$obsClient->getObject($bucket, "c.file", $options);
Common::println("b.file is fetched to the local file: c.file.localcopy");
Common::println("b.file is created");

// Copy an object
$result = $obsClient->copyObject($bucket, "c.file", $bucket, "c.file.copy");
Common::println("lastModifiedTime: " . $result[0]);
Common::println("ETag: " . $result[1]);

// Check whether an object exists
$doesExist = $obsClient->doesObjectExist($bucket, "c.file.copy");
Common::println("file c.file.copy exist? " . ($doesExist ? "yes" : "no"));

// Delete an object
$result = $obsClient->deleteObject($bucket, "c.file.copy");
Common::println("c.file.copy is deleted");
Common::println("b.file is created");
Common::println($result['x-obs-request-id']);

// Check whether an object exists
$doesExist = $obsClient->doesObjectExist($bucket, "c.file.copy");
Common::println("file c.file.copy exist? " . ($doesExist ? "yes" : "no"));

// Delete multiple objects in batch
$result = $obsClient->deleteObjects($bucket, array("b.file", "c.file"));
foreach($result as $object)
    Common::println($object);

sleep(2);
unlink("c.file.localcopy");

//******************************* For complete usage, see the following functions ****************************************************

listObjects($obsClient, $bucket);
listAllObjects($obsClient, $bucket);
createObjectDir($obsClient, $bucket);
putObject($obsClient, $bucket);
uploadFile($obsClient, $bucket);
getObject($obsClient, $bucket);
getObjectToLocalFile($obsClient, $bucket);
copyObject($obsClient, $bucket);
modifyMetaForObject($obsClient, $bucket);
getObjectMeta($obsClient, $bucket);
deleteObject($obsClient, $bucket);
deleteObjects($obsClient, $bucket);
doesObjectExist($obsClient, $bucket);
getSymlink($obsClient, $bucket);
putSymlink($obsClient, $bucket);
/**
 * Create a 'virtual' folder
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function createObjectDir($obsClient, $bucket)
{
    try {
        $obsClient->createObjectDir($bucket, "dir");
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Upload in-memory data to obs
 *
 * Simple upload---upload specified in-memory data to an OBS object
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function putObject($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    $content = file_get_contents(__FILE__);
    $options = array();
    try {
        $obsClient->putObject($bucket, $object, $content, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Uploads a local file to OBS
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function uploadFile($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    $filePath = __FILE__;
    $options = array();

    try {
        $obsClient->uploadFile($bucket, $object, $filePath, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Lists all files and folders in the bucket. 
 * Note if there's more items than the max-keys specified, the caller needs to use the nextMarker returned as the value for the next call's maker paramter.
 * Loop through all the items returned from ListObjects.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function listObjects($obsClient, $bucket)
{
    $prefix = 'obs-php-sdk-test/';
    $delimiter = '/';
    $nextMarker = '';
    $maxkeys = 1000;
    $options = array(
        'delimiter' => $delimiter,
        'prefix' => $prefix,
        'max-keys' => $maxkeys,
        'marker' => $nextMarker,
    );
    try {
        $listObjectInfo = $obsClient->listObjects($bucket, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    $objectList = $listObjectInfo->getObjectList(); // object list
    $prefixList = $listObjectInfo->getPrefixList(); // directory list
    if (!empty($objectList)) {
        print("objectList:\n");
        foreach ($objectList as $objectInfo) {
            print($objectInfo->getKey() . "\n");
        }
    }
    if (!empty($prefixList)) {
        print("prefixList: \n");
        foreach ($prefixList as $prefixInfo) {
            print($prefixInfo->getPrefix() . "\n");
        }
    }
}

/**
 * Lists all folders and files under the bucket. Use nextMarker repeatedly to get all objects.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function listAllObjects($obsClient, $bucket)
{
    // Create dir/obj 'folder' and put some files into it.
    for ($i = 0; $i < 100; $i += 1) {
        $obsClient->putObject($bucket, "dir/obj" . strval($i), "hi");
        $obsClient->createObjectDir($bucket, "dir/obj" . strval($i));
    }

    $prefix = 'dir/';
    $delimiter = '/';
    $nextMarker = '';
    $maxkeys = 30;

    while (true) {
        $options = array(
            'delimiter' => $delimiter,
            'prefix' => $prefix,
            'max-keys' => $maxkeys,
            'marker' => $nextMarker,
        );
        var_dump($options);
        try {
            $listObjectInfo = $obsClient->listObjects($bucket, $options);
        } catch (ObsException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        // Get the nextMarker, and it would be used as the next call's marker parameter to resume from the last call
        $nextMarker = $listObjectInfo->getNextMarker();
        $listObject = $listObjectInfo->getObjectList();
        $listPrefix = $listObjectInfo->getPrefixList();
        var_dump(count($listObject));
        var_dump(count($listPrefix));
        if ($nextMarker === '') {
            break;
        }
    }
}

/**
 * Get the content of an object.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObject($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    $options = array();
    try {
        $content = $obsClient->getObject($bucket, $object, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    if (file_get_contents(__FILE__) === $content) {
        print(__FUNCTION__ . ": FileContent checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FileContent checked FAILED" . "\n");
    }
}

/**
 * Put symlink
 *
 * @param ObsClient $obsClient  The Instance of ObsClient
 * @param string $bucket bucket name
 * @return null
 */
function putSymlink($obsClient, $bucket)
{
    $symlink = "test-samples-symlink";
    $object = "test-samples-object";
    try {
        $obsClient->putObject($bucket, $object, 'test-content');
        $obsClient->putSymlink($bucket, $symlink, $object);
        $content = $obsClient->getObject($bucket, $symlink);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    if ($content == 'test-content') {
        print(__FUNCTION__ . ": putSymlink checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": putSymlink checked FAILED" . "\n");
    }
}

/**
 * Get symlink
 *
 * @param ObsClient $obsClient  ObsClient instance
 * @param string $bucket  bucket name
 * @return null
 */
function getSymlink($obsClient, $bucket)
{
    $symlink = "test-samples-symlink";
    $object = "test-samples-object";
    try {
        $obsClient->putObject($bucket, $object, 'test-content');
        $obsClient->putSymlink($bucket, $symlink, $object);
        $content = $obsClient->getSymlink($bucket, $symlink);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    if ($content[ObsClient::OBS_SYMLINK_TARGET]) {
        print(__FUNCTION__ . ": getSymlink checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": getSymlink checked FAILED" . "\n");
    }
}

/**
 * Get_object_to_local_file
 *
 * Get object
 * Download object to a specified file.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObjectToLocalFile($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    $localfile = "upload-test-object-name.txt";
    $options = array(
        ObsClient::OBS_FILE_DOWNLOAD => $localfile,
    );

    try {
        $obsClient->getObject($bucket, $object, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK, please check localfile: 'upload-test-object-name.txt'" . "\n");
    if (file_get_contents($localfile) === file_get_contents(__FILE__)) {
        print(__FUNCTION__ . ": FileContent checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": FileContent checked FAILED" . "\n");
    }
    if (file_exists($localfile)) {
        unlink($localfile);
    }
}

/**
 * Copy object
 * When the source object is same as the target one, copy operation will just update the metadata.
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function copyObject($obsClient, $bucket)
{
    $fromBucket = $bucket;
    $fromObject = "obs-php-sdk-test/upload-test-object-name.txt";
    $toBucket = $bucket;
    $toObject = $fromObject . '.copy';
    $options = array();

    try {
        $obsClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $options);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Update Object Meta
 * it leverages the feature of copyObject： when the source object is just the target object, the metadata could be updated via copy
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function modifyMetaForObject($obsClient, $bucket)
{
    $fromBucket = $bucket;
    $fromObject = "obs-php-sdk-test/upload-test-object-name.txt";
    $toBucket = $bucket;
    $toObject = $fromObject;
    $copyOptions = array(
        ObsClient::OBS_HEADERS => array(
            'Cache-Control' => 'max-age=60',
            'Content-Disposition' => 'attachment; filename="xxxxxx"',
        ),
    );
    try {
        $obsClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject, $copyOptions);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Get object meta, that is, getObjectMeta
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function getObjectMeta($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    try {
        $objectMeta = $obsClient->getObjectMeta($bucket, $object);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    if (isset($objectMeta[strtolower('Content-Disposition')]) &&
        'attachment; filename="xxxxxx"' === $objectMeta[strtolower('Content-Disposition')]
    ) {
        print(__FUNCTION__ . ": ObjectMeta checked OK" . "\n");
    } else {
        print(__FUNCTION__ . ": ObjectMeta checked FAILED" . "\n");
    }
}

/**
 * Delete an object
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteObject($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    try {
        $obsClient->deleteObject($bucket, $object);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}


/**
 * Delete multiple objects in batch
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function deleteObjects($obsClient, $bucket)
{
    $objects = array();
    $objects[] = "obs-php-sdk-test/upload-test-object-name.txt";
    $objects[] = "obs-php-sdk-test/upload-test-object-name.txt.copy";
    try {
        $obsClient->deleteObjects($bucket, $objects);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * Check whether an object exists
 *
 * @param ObsClient $obsClient ObsClient instance
 * @param string $bucket bucket name
 * @return null
 */
function doesObjectExist($obsClient, $bucket)
{
    $object = "obs-php-sdk-test/upload-test-object-name.txt";
    try {
        $exist = $obsClient->doesObjectExist($bucket, $object);
    } catch (ObsException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    print(__FUNCTION__ . ": OK" . "\n");
    var_dump($exist);
}

