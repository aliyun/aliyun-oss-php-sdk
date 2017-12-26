<?php

namespace OSS\Tests;

use OSS\Result\SymlinkResult;
use OSS\Core\OssException;
use OSS\Http\ResponseCore;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';

class SymlinkTest extends TestOssClientBase
{
    public function testPutNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = '_link@/';
        $object_not_exist = 'not_exist_object+$#!b不';
        $this->ossClient->putSymlink($bucket, $symlink, $object_not_exist);

        try{
            $result = $this->ossClient->getObject($bucket, $symlink);
            $this->assertTrue(false);
        }catch (OssException $e){
            $this->assertEquals('The symlink target object does not exist', $e->getErrorMessage());
        }
    }

    public function testPutNotNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'test-link=()*&';
        $object = 'exist_object^$#!~';

        $this->ossClient ->putObject($bucket, $object,'test_content');
        $this->ossClient->putSymlink($bucket, $symlink, $object);
        $result = $this->ossClient->getObject($bucket, $symlink);
        $this->assertEquals('test_content', $result);
    }

    public function testGetNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'null_link@/';
        try{
            $result = $this->ossClient->getSymlink($bucket, $symlink);
            $this->assertTrue(false);
        }catch (OssException $e){
            $this->assertEquals('The specified key does not exist.', $e->getErrorMessage());
        }
    }

    public function testGetNotNullSymlink()
    {
        $bucket = getenv('OSS_BUCKET');
        $symlink = 'test-link=()*&';
        $object = 'exist_object^$#!~';
        $result = $this->ossClient->getSymlink($bucket, $symlink);
        $this->assertEquals($result["x-oss-symlink-target"], $object);
    }



}
