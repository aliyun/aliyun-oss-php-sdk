<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Credentials\StaticCredentialsProvider;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleRule;
use OSS\Model\LifecycleAction;
use OSS\OssClient;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientForcePathStyleTest extends TestOssClientBase
{
    public function testForcePathStyle()
    {
        $accessKeyId = getenv("OSS_ACCESS_KEY_ID");
        $accessKeySecret = getenv("OSS_ACCESS_KEY_SECRET");
        $endpoint = getenv('OSS_ENDPOINT');
        $provider = new StaticCredentialsProvider($accessKeyId, $accessKeySecret);
        $config = array(
            'endpoint' => $endpoint,
            'provider' => $provider,
            'hostType' => OssClient::OSS_HOST_TYPE_PATH_STYLE,
        );
        $ossClient = new OssClient($config);
        try {
            $ossClient->getBucketInfo($this->bucket);
        } catch (OssException $e) {
            $this->assertEquals($e->getErrorCode(), "SecondLevelDomainForbidden");
            $this->assertTrue(true);
        }

        try {
            $object = "oss-php-sdk-test/upload-test-object-name.txt";
            $ossClient->putObject($this->bucket, $object, 'hi oss');
        } catch (OssException $e) {
            $this->assertEquals($e->getErrorCode(), "SecondLevelDomainForbidden");
            $this->assertTrue(true);
        }

        try {
            $strUrl = $endpoint . "/" . $this->bucket . "/" . $object;
            $signUrl = $ossClient->signUrl($this->bucket, $object, 3600);
            $this->assertTrue(strpos($signUrl, $strUrl) !== false);
        } catch (OssException $e) {
            $this->assertFalse(true);
        }
    }
}
