<?php

namespace OSS\Tests;

use OSS\Result\SymlinkResult;
use OSS\Core\OssException;
use OSS\Http\ResponseCore;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class SymlinkTest extends TestOssClientBase
{
    public function testPutSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'test-link';
        $special_object = 'exist_object^$#!~';
        $object = 'exist_object';

        $this->ossClient ->putObject($bucket, $object, 'test_content');
        $this->ossClient->putSymlink($bucket, $symlink, $object);
        $result = $this->ossClient->getObject($bucket, $symlink);
        $this->assertEquals('test_content', $result);

        $this->ossClient ->putObject($bucket, $special_object, 'test_content');
        $this->ossClient->putSymlink($bucket, $symlink, $special_object);
        $result = $this->ossClient->getObject($bucket, $symlink);
        $this->assertEquals('test_content', $result);
    }

    public function testGetSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'test-link';
        $object = 'exist_object^$#!~';

        $result = $this->ossClient->getSymlink($bucket, $symlink);
        $this->assertEquals($result["x-oss-symlink-target"], $object);
        $this->assertEquals('200', $result['info']['http_code']);
        $this->assertTrue(isset($result['etag']));
        $this->assertTrue(isset($result['x-oss-request-id']));
    }

    public function testPutNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'null-link';
        $object_not_exist = 'not_exist_object+$#!b不';
        $this->ossClient->putSymlink($bucket, $symlink, $object_not_exist);

        try{
            $this->ossClient->getObject($bucket, $symlink);
            $this->assertTrue(false);
        }catch (OssException $e){
            $this->assertEquals('The symlink target object does not exist', $e->getErrorMessage());
        }
    }

    public function testGetNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'null-link-new';

        try{
            $result = $this->ossClient->getSymlink($bucket, $symlink);
            $this->assertTrue(false);
        }catch (OssException $e){
            $this->assertEquals('The specified key does not exist.', $e->getErrorMessage());
        }
    }
}
