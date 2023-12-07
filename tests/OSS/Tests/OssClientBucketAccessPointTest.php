<?php

namespace OSS\Tests;

use OSS\Model\AccessPointConfig;
use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'TestOssClientBase.php';


class OssClientBucketAccessPointTest extends TestOssClientBase
{
    public function testBucketAccessPoint()
    {
        $accountId = getenv('OSS_TEST_ACCOUNT_ID');
        if ($accountId == ""){
            $this->fail("account id is empty!");
        }

        try {
            $apName = 'ap1-'.time();
            $net = "vpc";
            $vpcId = "vpc-123456789";
            $accessConfig = new AccessPointConfig($apName,$net,$vpcId);
            $result = $this->ossClient->putBucketAccessPoint($this->bucket,$accessConfig);
            $this->assertNotNull($result->getAccessPointArn());
            $this->assertNotNull($result->getAlias());
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        Common::waitMetaSync();
        try {
            $res = $this->ossClient->getBucketAccessPoint($this->bucket,$apName);
            $this->assertEquals($res->getAccessPointName(),$apName);
            $this->assertEquals($res->getBucket(),$this->bucket);
            $this->assertNotNull($res->getAccountId());
            $this->assertEquals($res->getNetworkOrigin(),$net);
            $this->assertEquals($res->getVpcId(),$vpcId);
            $this->assertNotNull($res->getAccessPointArn());
            $this->assertNotNull($res->getStatus());
            $this->assertNotNull($res->getCreationDate());
            $this->assertNotNull($res->getAlias());
            $this->assertNotNull($res->getInternalEndpoint());
            $this->assertNotNull($res->getPublicEndpoint());
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $apName2 = 'ap2-'.time();
            $net2 = "internet";
            $accessConfig = new AccessPointConfig($apName2,$net2);
            $result = $this->ossClient->putBucketAccessPoint($this->bucket,$accessConfig);
            $this->assertNotNull($result->getAccessPointArn());
            $this->assertNotNull($result->getAlias());
            $alias = $result->getAlias();
            $accessPointArn = $result->getAccessPointArn();
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }
        Common::waitMetaSync();
        try {
            $list = $this->ossClient->listBucketAccessPoint($this->bucket);
            $this->assertNotNull($list->getAccountId());
            $this->assertNull($list->getNextContinuationToken());
            $this->assertEquals($list->getIsTruncated(),false);
            $accessPoints = $list->getAccessPoints();
            $this->assertEquals(2, count($accessPoints));
            $this->assertEquals($apName, $accessPoints[0]->getAccessPointName());
            $this->assertEquals($this->bucket, $accessPoints[0]->getBucket());
            $this->assertNotNull($accessPoints[0]->getAlias());
            $this->assertEquals("vpc-123456789", $accessPoints[0]->getVpcId());
            $this->assertEquals("vpc", $accessPoints[0]->getNetworkOrigin());
            $this->assertNotNull($accessPoints[0]->getStatus());

            $this->assertEquals($apName2, $accessPoints[1]->getAccessPointName());
            $this->assertEquals($this->bucket, $accessPoints[1]->getBucket());
            $this->assertNotNull($accessPoints[1]->getAlias());
            $this->assertEquals(null, $accessPoints[1]->getVpcId());
            $this->assertEquals("internet", $accessPoints[1]->getNetworkOrigin());
            $this->assertNotNull($accessPoints[1]->getStatus());
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        while (true){
            $res = $this->ossClient->getBucketAccessPoint($this->bucket,$apName);
            if ($res->getStatus() == 'enable'){
                break;
            }
            sleep(20);
        }
        $policy = <<<BBB
 {
   "Version":"1",
   "Statement":[
   {
     "Action":[
       	"oss:*"
    ],
    "Effect": "Allow",
    "Principal":["$accountId"],
    "Resource":[
		"$accessPointArn",
		"$accessPointArn/object/*"
     ]
   }
  ]
 }
BBB;
        try {
            $this->ossClient->putAccessPointPolicy($this->bucket,$apName2,$policy);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }
        Common::waitMetaSync();

        try {
            $info = $this->ossClient->getAccessPointPolicy($this->bucket,$apName2);
            $this->assertNotNull($info);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }
        $policy2 = <<<BBB
 {
   "Version":"1",
   "Statement":[
   {
     "Action":[
       	"oss:*"
    ],
    "Effect": "Allow",
    "Principal":["$accountId"],
    "Resource":[
		"$accessPointArn",
		"$accessPointArn/object/*"
     ]
   },
	{
     "Action":[
       "oss:PutObject",
       "oss:GetObject"
    ],
    "Effect":"Deny",
    "Principal":["123456"],
    "Resource":[
        "$accessPointArn",
		"$accessPointArn/object/*"
     ]
   }
  ]
 }
BBB;

        while (true){
            $res = $this->ossClient->getBucketAccessPoint($this->bucket,$apName2);
            if ($res->getStatus() == 'enable'){
                break;
            }
            sleep(20);
        }
        try {
            $this->ossClient->putAccessPointPolicy($alias,$apName2,$policy2);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }
        Common::waitMetaSync();

        try {
            $this->ossClient->getAccessPointPolicy($alias,$apName2);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->deleteAccessPointPolicy($alias,$apName2);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $this->ossClient->putAccessPointPolicy($this->bucket,$apName2,$policy2);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }
        Common::waitMetaSync();

        try {
            $this->ossClient->deleteAccessPointPolicy($this->bucket,$apName);
            $this->assertTrue(true);
        }catch (OssException $e){
            printf($e->getMessage());
            $this->assertTrue(false);
        }


        while (true){
            try {
                $list = $this->ossClient->listBucketAccessPoint($this->bucket);
                $accessPoints = $list->getAccessPoints();
                if (isset($accessPoints) && count($accessPoints) > 0){
                    foreach ($accessPoints as $accessPoint){
                        if ($accessPoint->getStatus() == "enable"){
                            $this->ossClient->deleteBucketAccessPoint($this->bucket,$accessPoint->getAccessPointName());
                        }
                    }
                }else{
                    break;
                }
                sleep(30);
            }catch (OssException $e){
                printf($e->getMessage());
                $this->assertTrue(false);
            }

        }
    }
}
