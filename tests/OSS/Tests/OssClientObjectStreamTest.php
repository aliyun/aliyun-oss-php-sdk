<?php
namespace OSS\Tests;

use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientObjectStreamTest extends TestOssClientBase
{

    public function testStream(){
        $object = "oss-php-sdk-test/upload-test-object-name.txt";
        $stream = fopen(__FILE__,'r');
        $options = array();
        try {
            $this->ossClient->putStream($this->bucket, $object, $stream, $options);
            if (is_resource($stream)){
                @fclose($stream);
            }
        } catch (OssException $e) {
            $this->assertTrue(false);
        }


        try {
            Common::waitMetaSync();
            $result = $this->ossClient->getStream($this->bucket, $object, $options);
            while(!$result->eof()) {
                $rs =  $result->read(108);
                var_dump($rs);
                $this->assertNotNull($rs);
            }
        } catch (OssException $e) {
            $this->assertTrue(false);
        }

    }
}
