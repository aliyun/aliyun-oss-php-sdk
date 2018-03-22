<?php

namespace OSS\Tests;

use OSS\Result\AclResult;
use OSS\Core\OssException;
use OSS\Http\ResponseCore;
use OSS\OssClient;

class AclResultTest extends \PHPUnit_Framework_TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" ?>
<AccessControlPolicy>
    <Owner>
        <ID>00220120222</ID>
        <DisplayName>user_example</DisplayName>
    </Owner>
    <AccessControlList>
        <Grant>public-read</Grant>
    </AccessControlList>
</AccessControlPolicy>
BBBB;

    private $invalidXml = <<<BBBB
<?xml version="1.0" ?>
<AccessControlPolicy>
</AccessControlPolicy>
BBBB;

    public static function setUpBeforeClass()
    {
        $accessKeyId = ' ' . getenv('OSS_ACCESS_KEY_ID') . ' ';
        $accessKeySecret = ' ' . getenv('OSS_ACCESS_KEY_SECRET') . ' ';
        $endpoint = ' ' . getenv('OSS_ENDPOINT') . '/ ';
        $bucket = getenv('BUCKET_NAME_PREFIX').'-'.getenv('OSS_BUCKET');

        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);

        $ossClient ->createBucket($bucket);
        sleep(3);
    }

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new AclResult($response);
        $this->assertEquals("public-read", $result->getData());
    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), "", 200);
        try {
            new AclResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertEquals('body is null', $e->getMessage());
        }
    }

    public function testParseInvalidXml()
    {
        $response = new ResponseCore(array(), $this->invalidXml, 200);
        try {
            new AclResult($response);
            $this->assertFalse(true);
        } catch (OssException $e) {
            $this->assertEquals("xml format exception", $e->getMessage());
        }
    }
}
