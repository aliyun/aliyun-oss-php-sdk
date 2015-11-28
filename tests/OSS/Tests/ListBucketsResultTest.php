<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Http\ResponseCore;
use OSS\Result\ListBucketsResult;

class ListBucketsResultTest extends \PHPUnit_Framework_TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<ListAllMyBucketsResult>
  <Owner>
    <ID>ut_test_put_bucket</ID>
    <DisplayName>ut_test_put_bucket</DisplayName>
  </Owner>
  <Buckets>
    <Bucket>
      <Location>oss-cn-hangzhou-a</Location>
      <Name>xz02tphky6fjfiuc0</Name>
      <CreationDate>2014-05-15T11:18:32.000Z</CreationDate>
    </Bucket>
    <Bucket>
      <Location>oss-cn-hangzhou-a</Location>
      <Name>xz02tphky6fjfiuc1</Name>
      <CreationDate>2014-05-15T11:18:32.000Z</CreationDate>
    </Bucket>
  </Buckets>
</ListAllMyBucketsResult>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<ListAllMyBucketsResult>
  <Owner>
    <ID>ut_test_put_bucket</ID>
    <DisplayName>ut_test_put_bucket</DisplayName>
  </Owner>
  <Buckets>
  </Buckets>
</ListAllMyBucketsResult>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListBucketsResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $bucketListInfo = $result->getData();
        $this->assertEquals(2, count($bucketListInfo->getBucketList()));
    }

    public function testParseNullXml()
    {
        $response = new ResponseCore(array(), $this->nullXml, 200);
        $result = new ListBucketsResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $bucketListInfo = $result->getData();
        $this->assertEquals(0, count($bucketListInfo->getBucketList()));
    }

    public function test403()
    {
        $response = new ResponseCore(array(), "", 403);
        try {
            new ListBucketsResult($response);
        } catch (OssException $e) {
            $this->assertEquals($e->getMessage(), 'http status: 403, Reason: authorization forbidden, please check your AccessKeyId and AccessKeySecret');
        }
    }
}
