<?php

if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
require_once __DIR__ . '/Config.php';

use OBS\ObsClient;
use OBS\Core\ObsException;

/**
 * Class Common
 *
 * The Common class for 【Samples/*.php】 used to obtain ObsClient instance and other common functions
 */
class Common
{
    const endpoint = Config::OBS_ENDPOINT;
    const accessKeyId = Config::OBS_ACCESS_ID;
    const accessKeySecret = Config::OBS_ACCESS_KEY;
    const bucket = Config::OBS_TEST_BUCKET;

    /**
     * Get an OBSClient instance according to config.
     *
     * @return ObsClient An ObsClient instance
     */
    public static function getObsClient()
    {
        try {
            $obsClient = new ObsClient(self::accessKeyId, self::accessKeySecret, self::endpoint, false);
        } catch (ObsException $e) {
            printf(__FUNCTION__ . "creating ObsClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $obsClient;
    }

    public static function getBucketName()
    {
        return self::bucket;
    }

    /**
     * A tool function which creates a bucket and exists the process if there are exceptions
     */
    public static function createBucket()
    {
        $obsClient = self::getObsClient();
        if (is_null($obsClient)) exit(1);
        $bucket = self::getBucketName();
        $acl = ObsClient::OBS_ACL_TYPE_PUBLIC_READ;
        try {
            $obsClient->createBucket($bucket, $acl);
        } catch (ObsException $e) {

            $message = $e->getMessage();
            if (\OBS\Core\ObsUtil::startsWith($message, 'http status: 403')) {
                echo "Please Check your AccessKeyId and AccessKeySecret" . "\n";
                exit(0);
            } elseif (strpos($message, "BucketAlreadyExists") !== false) {
                echo "Bucket already exists. Please check whether the bucket belongs to you, or it was visited with correct endpoint. " . "\n";
                exit(0);
            }
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }

    public static function println($message)
    {
        if (!empty($message)) {
            echo strval($message) . "\n";
        }
    }
}

# Common::createBucket();
