<?php

namespace OSS\Tests;

use OSS\OssClient;
use OSS\Core\OssException;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'StsClient.php';

Class StTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public $accessKeyId;

    public $accessKeySecret;

    public $securityToken;

    public $endpoint;

    public function setUp()
    {
        date_default_timezone_set("UTC");
    }

    public function  testAssumeRole()
    {
        $this->client = new StsClient();
        $assumeRole = new AssumeRole();
        $assumeRole->Timestamp = date("Y-m-d")."H".date("h:i:s")."Z";
        $assumeRole->AccessKeyId = getenv('OSS_STS_ID');
        $assumeRole->SignatureNonce = time();
        $assumeRole->RoleSessionName = "sts";
        $assumeRole->RoleArn = getenv('OSS_STS_ARN');
        $params = $assumeRole->getAttributes();
        $response = $this->client->doAction($params);
        $this->assertTrue(isset($response->AssumedRoleUser));
        $this->assertTrue(isset($response->Credentials));

        $time = substr($response->Credentials->Expiration, 0, 10).' '.substr($response->Credentials->Expiration, 11, 8);
        $this->assertEquals(strtotime($time)-strtotime("now"),3600);

        $this->accessKeyId = $response->Credentials->AccessKeyId;
        $this->accessKeySecret = $response->Credentials->AccessKeySecret;
        $this->securityToken = $response->Credentials->SecurityToken;
        $this->endpoint = "http://oss-cn-hangzhou.aliyuncs.com";

        $content = "test-content";
        $key = "test-sts";
        $bucket = "sts-test-bucket-123456";

        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, false, $this->securityToken);

        $ossClient->createBucket($bucket);

        $ossClient->putObject($bucket, $key, $content);

        $result = $ossClient->getObject($bucket, $key);
        $this->assertEquals($content, $result);

        // list object
        $objectListInfo = $ossClient->listObjects($bucket);
        $objectList = $objectListInfo->getObjectList();
        $this->assertNotNull($objectList);
        $this->assertTrue(is_array($objectList));
        $objects = array();
        foreach ($objectList as $value) {
            $objects[] = $value->getKey();
        }
        $this->assertEquals(1, count($objects));
        $this->assertTrue(in_array($key, $objects));

        $ossClient->deleteObject($bucket, $key);

        $ossClient->deleteBucket($bucket);

    }

    public function  testGetCallerIdentity()
    {
        $this->client = new StsClient();
        $callerIdentity = new GetCallerIdentity();
        $callerIdentity->Timestamp = date("Y-m-d")."H".date("h:i:s")."Z";
        $callerIdentity->AccessKeyId = getenv('OSS_STS_ID');
        $callerIdentity->SignatureNonce = time();
        $params = $callerIdentity->getAttributes();
        $response = $this->client->doAction($params);
        $this->assertTrue(isset($response->AccountId));
        $this->assertTrue(isset($response->Arn));
        $this->assertTrue(isset($response->RequestId));
        $this->assertTrue(isset($response->UserId));
    }

    public function testAssumeRoleNegative()
    {
        $this->client = new StsClient();
        //AccessKeyId invalid
        $assumeRole = new AssumeRole();
        $assumeRole->Timestamp = date("Y-m-d")."H".date("h:i:s")."Z";
        $assumeRole->AccessKeyId = "";
        $assumeRole->SignatureNonce = time();
        $assumeRole->RoleSessionName = "sts";
        $assumeRole->RoleArn = getenv('OSS_STS_ARN');
        $params = $assumeRole->getAttributes();
        try{
            $response = $this->client->doAction($params);
            $this->assertTrue(false);
        }catch(OssException $e){
            $this->assertEquals("InvalidAccessKeyId.NotFound", $e->getMessage());
        }

        //RoleArn invalid
        $assumeRole = new AssumeRole();
        $assumeRole->Timestamp = date("Y-m-d")."H".date("h:i:s")."Z";
        $assumeRole->AccessKeyId = getenv('OSS_STS_ID');
        $assumeRole->SignatureNonce = time()."df";
        $assumeRole->RoleSessionName = "sts";
        $assumeRole->RoleArn = "d";
        $params = $assumeRole->getAttributes();
        try{
            $response = $this->client->doAction($params);
            $this->assertTrue(false);
        }catch(OssException $e){

            $this->assertEquals("InvalidParameter.RoleArn", $e->getMessage());
        }

        //InvalidTimeStamp
        $assumeRole = new AssumeRole();
        $assumeRole->Timestamp = "2017-03-12"."H".date("h:i:s")."Z";
        $assumeRole->AccessKeyId = getenv('OSS_STS_ID');
        $assumeRole->SignatureNonce = time();
        $assumeRole->RoleSessionName = "sts";
        $assumeRole->RoleArn = getenv('OSS_STS_ARN');
        $params = $assumeRole->getAttributes();
        try{
            $response = $this->client->doAction($params);
            $this->assertTrue(false);
        }catch(OssException $e){
            $this->assertEquals("InvalidTimeStamp.Expired", $e->getMessage());
        }
    }
}
