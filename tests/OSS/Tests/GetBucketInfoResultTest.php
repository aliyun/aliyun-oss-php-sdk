<?php

namespace OSS\Tests;


use OSS\Http\ResponseCore;
use OSS\Result\GetBucketInfoResult;

class GetBucketInfoResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<BucketInfo>
  <Bucket>
    <Comment></Comment>
    <CreationDate>2023-02-14T06:56:02.000Z</CreationDate>
    <CrossRegionReplication>Disabled</CrossRegionReplication>
    <DataRedundancyType>LRS</DataRedundancyType>
    <ExtranetEndpoint>11.158.239.225</ExtranetEndpoint>
    <IntranetEndpoint>11.158.239.225</IntranetEndpoint>
    <Location>oss-cn-hangzhou-pocarchive</Location>
    <Name>mxx-testrc1</Name>
    <ReservedCapacityInstanceId>7e6cb0c5-efaf-45f4-bc39-ab8e342cbf1c</ReservedCapacityInstanceId>
    <StorageClass>ReservedCapacity</StorageClass>
    <TransferAcceleration>Disabled</TransferAcceleration>
    <Owner>
      <DisplayName>1422558957716563</DisplayName>
      <ID>1422558957716563</ID>
    </Owner>
    <AccessControlList>
      <Grant>private</Grant>
    </AccessControlList>
    <ServerSideEncryptionRule>
      <SSEAlgorithm>None</SSEAlgorithm>
    </ServerSideEncryptionRule>
    <BucketPolicy>
      <LogBucket></LogBucket>
      <LogPrefix></LogPrefix>
    </BucketPolicy>
  </Bucket>
</BucketInfo>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketInfo>
  <Bucket>
    <AccessMonitor>Enabled</AccessMonitor>
    <CreationDate>2013-07-31T10:56:21.000Z</CreationDate>
    <ExtranetEndpoint>oss-cn-hangzhou.aliyuncs.com</ExtranetEndpoint>
    <IntranetEndpoint>oss-cn-hangzhou-internal.aliyuncs.com</IntranetEndpoint>
    <Location>oss-cn-hangzhou</Location>
    <StorageClass>Standard</StorageClass>
    <TransferAcceleration>Disabled</TransferAcceleration>
    <CrossRegionReplication>Disabled</CrossRegionReplication>
    <Name>oss-example</Name>
    <ResourceGroupId>rg-aek27tc********</ResourceGroupId>
    <Owner>
      <DisplayName>username</DisplayName>
      <ID>27183473914****</ID>
    </Owner>
    <AccessControlList>
      <Grant>private</Grant>
    </AccessControlList>  
    <Comment>test</Comment>
    <BucketPolicy>
      <LogBucket>examplebucket</LogBucket>
      <LogPrefix>log/</LogPrefix>
    </BucketPolicy>
  </Bucket>
</BucketInfo>
BBBB;


    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketInfoResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $info = $result->getData();

        $this->assertEquals($info->getName(),"mxx-testrc1");
        $this->assertEquals($info->getLocation(),"oss-cn-hangzhou-pocarchive");
        $this->assertEquals($info->getCreateDate(),"2023-02-14T06:56:02.000Z");
        $this->assertEquals($info->getStorageClass(),"ReservedCapacity");
        $this->assertEquals($info->getExtranetEndpoint(),"11.158.239.225");
        $this->assertEquals($info->getIntranetEndpoint(),"11.158.239.225");
        $this->assertEquals($info->getReservedCapacityInstanceId(),"7e6cb0c5-efaf-45f4-bc39-ab8e342cbf1c");
    }


    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetBucketInfoResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $info = $result->getData();

        $this->assertNull($info->getReservedCapacityInstanceId());
    }
}
