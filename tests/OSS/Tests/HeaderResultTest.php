<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Result\ExistResult;
use OSS\Result\HeaderResult;
use OSS\Http\ResponseCore;

/**
 * Class HeaderResultTest
 * @package OSS\Tests
 */
class HeaderResultTest extends \PHPUnit\Framework\TestCase
{
    public function testGetHeader()
    {
        $response = new ResponseCore(array('key' => 'value'), "", 200);
        $result = new HeaderResult($response);
        $this->assertTrue($result->isOK());
        $this->assertTrue(is_array($result->getData()));
        $data = $result->getData();
        $this->assertEquals($data['key'], 'value');
    }
    public function testGetHeader2()
    {
        $xml = '<?xml version="1.0" ?>
<Error xmlns="http://doc.oss-cn-hangzhou.aliyuncs.com">
  <Code>AccessDenied</Code>
  <Message>***</Message>
  <RequestId>*******</RequestId>
  <HostId>oss-cn-hangzhou.aliyuncs.com</HostId>
  <EC>0003-00000016</EC>
</Error>';
        $header = array(
            "x-oss-request-id"=>"636B68BA80DA8539399F2397",
            "x-oss-server-time"=>0,
            "x-oss-ec"=>"0003-00000016",
            "x-oss-err"=>base64_encode($xml),
        );
        $response = new ResponseCore($header, "", 403);


        try {
            $result = new HeaderResult($response);
        }catch (OssException $e){
            $this->assertEquals($e->getEc(),"0003-00000016");
            $this->assertEquals($e->getErrorMessage(),"***");
            $this->assertEquals($e->getErrorCode(),"AccessDenied");
        }
    }


    public function testIsExist()
    {
        $xml = '<?xml version="1.0" ?>
<Error xmlns="http://doc.oss-cn-hangzhou.aliyuncs.com">
  <Code>NotSuchKey</Code>
  <Message>not exist</Message>
  <RequestId>11111111111111111111111</RequestId>
  <HostId>oss-cn-hangzhou.aliyuncs.com</HostId>
  <EC>0003-00000016</EC>
</Error>';
        $header = array(
            "x-oss-request-id"=>"636B68BA80DA8539399F2397",
            "x-oss-server-time"=>0,
            "x-oss-ec"=>"0003-00000016",
            "x-oss-err"=>base64_encode($xml),
        );
        $response = new ResponseCore($header, "", 404);


        try {
            $result = new ExistResult($response);
            $this->assertTrue($result->isOK());
            $this->assertEquals($result->getData(), false);
        }catch (OssException $e){
            $this->assertTrue(false);
        }
    }
}
