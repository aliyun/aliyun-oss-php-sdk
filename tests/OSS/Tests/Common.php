<?php

namespace OSS\Tests;

require_once __DIR__ . '/../../../autoload.php';

use OSS\Crypto\BaseCryptoProvider;
use Oss\Crypto\KmsProvider;
use OSS\Crypto\RsaProvider;
use OSS\OssClient;
use OSS\Core\OssException;
use Oss\OssEncryptionClient;

/**
 * Class Common
 *
 * Sample program [Samples / *. Php] Common class, used to obtain OssClient instance and other public methods
 */
class Common
{
    /**
     * According to the Config configuration, get an OssClient instance
     *
     * @return OssClient  An OssClient instance
     */
    public static function getOssClient()
    {
        try {
            $ossClient = new OssClient(
                getenv('OSS_ACCESS_KEY_ID'),
                getenv('OSS_ACCESS_KEY_SECRET'),
                getenv('OSS_ENDPOINT'), false);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }


    /**
     * @param KmsProvider|RsaProvider $provider
     * @return OssEncryptionClient|null
     */
    public static function getOssEncryptionClient($provider)
    {
        if (!$provider instanceof BaseCryptoProvider){
            throw new OssException('Crypto provider must be an instance of BaseCryptoProvider');
        }
        try {
            $ossClient = new OssEncryptionClient( getenv('OSS_ACCESS_KEY_ID'),
                getenv('OSS_ACCESS_KEY_SECRET'),
                getenv('OSS_ENDPOINT'), $provider);
        } catch (OssException $e) {
            printf(__FUNCTION__ . "creating OssClient instance: FAILED\n");
            printf($e->getMessage() . "\n");
            return null;
        }
        return $ossClient;
    }


    public static function getAccessKeyId()
    {
        return getenv('OSS_ACCESS_KEY_ID');
    }

    public static function getAccessKeySecret()
    {
        return getenv('OSS_ACCESS_KEY_SECRET');
    }

    public static function getEndPoint()
    {
        return getenv('OSS_ENDPOINT');
    }

    public static function getBucketName()
    {
        return getenv('OSS_BUCKET');
    }

    public static function getRegion()
    {
		return getenv('OSS_REGION'); 
    }

	public static function getCallbackUrl()
    {
        return getenv('OSS_CALLBACK_URL');
    }

    public static function getKmsEndPoint()
    {
        return getenv('KMS_ENDPOINT');;
    }

    public static function getKmsId()
    {
        return getenv('KMS_ID');
    }

    public static function getKmsEndPointOther()
    {
        return getenv('KMS_ENDPOINT_OTHER');
    }

    public static function getKmsIdOther()
    {
        return getenv('KMS_ID_OTHER');
    }

    /**
     * Tool method, create a bucket
     */
    public static function createBucket()
    {
        $ossClient = self::getOssClient();
        if (is_null($ossClient)) exit(1);
        $bucket = self::getBucketName();
        $acl = OssClient::OSS_ACL_TYPE_PUBLIC_READ;
        try {
            $ossClient->createBucket($bucket, $acl);
        } catch (OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }

    /**
     * Wait for bucket meta sync
     */
    public static function waitMetaSync()
    {
        if (getenv('TRAVIS')) {
            sleep(10);
        }
    }
}
