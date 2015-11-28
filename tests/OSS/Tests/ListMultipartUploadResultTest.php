<?php

namespace OSS\Tests;

use OSS\Result\ListMultipartUploadResult;
use OSS\Http\ResponseCore;

/**
 * Class ListMultipartUploadResultTest
 * @package OSS\Tests
 */
class ListMultipartUploadResultTest extends \PHPUnit_Framework_TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<ListMultipartUploadsResult xmlns="http://doc.oss-cn-hangzhou.aliyuncs.com">
    <Bucket>oss-example</Bucket>
    <KeyMarker>xx</KeyMarker>
    <UploadIdMarker>3</UploadIdMarker>
    <NextKeyMarker>oss.avi</NextKeyMarker>
    <NextUploadIdMarker>0004B99B8E707874FC2D692FA5D77D3F</NextUploadIdMarker>
    <Delimiter>x</Delimiter>
    <Prefix>xx</Prefix>
    <MaxUploads>1000</MaxUploads>
    <IsTruncated>false</IsTruncated>
    <Upload>
        <Key>multipart.data</Key>
        <UploadId>0004B999EF518A1FE585B0C9360DC4C8</UploadId>
        <Initiated>2012-02-23T04:18:23.000Z</Initiated>
    </Upload>
    <Upload>
        <Key>multipart.data</Key>
        <UploadId>0004B999EF5A239BB9138C6227D69F95</UploadId>
        <Initiated>2012-02-23T04:18:23.000Z</Initiated>
    </Upload>
    <Upload>
        <Key>oss.avi</Key>
        <UploadId>0004B99B8E707874FC2D692FA5D77D3F</UploadId>
        <Initiated>2012-02-23T06:14:27.000Z</Initiated>
    </Upload>
</ListMultipartUploadsResult>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new ListMultipartUploadResult($response);
        $listMultipartUploadInfo = $result->getData();
        $this->assertEquals("oss-example", $listMultipartUploadInfo->getBucket());
        $this->assertEquals("xx", $listMultipartUploadInfo->getKeyMarker());
        $this->assertEquals(3, $listMultipartUploadInfo->getUploadIdMarker());
        $this->assertEquals("oss.avi", $listMultipartUploadInfo->getNextKeyMarker());
        $this->assertEquals("0004B99B8E707874FC2D692FA5D77D3F", $listMultipartUploadInfo->getNextUploadIdMarker());
        $this->assertEquals("x", $listMultipartUploadInfo->getDelimiter());
        $this->assertEquals("xx", $listMultipartUploadInfo->getPrefix());
        $this->assertEquals(1000, $listMultipartUploadInfo->getMaxUploads());
        $this->assertEquals("false", $listMultipartUploadInfo->getIsTruncated());
        $this->assertEquals("multipart.data", $listMultipartUploadInfo->getUploads()[0]->getKey());
        $this->assertEquals("0004B999EF518A1FE585B0C9360DC4C8", $listMultipartUploadInfo->getUploads()[0]->getUploadId());
        $this->assertEquals("2012-02-23T04:18:23.000Z", $listMultipartUploadInfo->getUploads()[0]->getInitiated());
    }
}
