<?php
namespace OSS\Tests;

include_once __DIR__ . DIRECTORY_SEPARATOR.'../../../vendor/openaliyuns/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';

use Sts\Request\V20150401 as Sts;

class StsTest extends \PHPUnit_Framework_TestCase
{
    private $client;

    public function setUp()
    {
        date_default_timezone_set("UTC");
        define("REGION_ID", "cn-shanghai");
        define("ENDPOINT", "sts.cn-shanghai.aliyuncs.com");
        define("ACCESS_KEY_ID", "LTAIVdxMrOBUSWoS");
        define("ACCESS_KEY_SECRET", "vtGoCcfxjf76gK2ZTwHabtRaUPzlfQ");
        define("CLIENT_NAME", "sts");
        define("EXPIRE_TIME", "3600");
        define("ROLE_ARN", "acs:ram::1521081174204619:role/test");
        $policy = <<<POLICY
                    {
                      "Statement": [
                        {
                          "Action": [
                            "oss:Get*",
                            "oss:List*"
                          ],
                          "Effect": "Allow",
                          "Resource": "*"
                        }
                      ],
                      "Version": "1"
                    }
POLICY;
        define("POLICY", $policy);

        \DefaultProfile::addEndpoint(REGION_ID, REGION_ID, "Sts", ENDPOINT);
        $iClientProfile = \DefaultProfile::getProfile(REGION_ID, ACCESS_KEY_ID, ACCESS_KEY_SECRET);
        $this->client = new \DefaultAcsClient($iClientProfile);

    }

    public function  testAssumeRole()
    {
        $request = new Sts\AssumeRoleRequest();
        $request->setRoleSessionName(CLIENT_NAME);
        $request->setRoleArn(ROLE_ARN);
        $request->setPolicy(POLICY);
        $request->setDurationSeconds(EXPIRE_TIME);
        $response = $this->client->getAcsResponse($request);
        $this->assertTrue(isset($response->AssumedRoleUser));
        $this->assertTrue(isset($response->Credentials));
        $this->assertEquals($response->AssumedRoleUser->Arn, ROLE_ARN.'/'.CLIENT_NAME);
        $time = substr($response->Credentials->Expiration, 0, 10).' '.substr($response->Credentials->Expiration, 11, 8);
        $this->assertEquals(strtotime($time)-strtotime("now"),EXPIRE_TIME);
    }

    public function testGetCallerIdentity()
    {
        $request = new Sts\GetCallerIdentityRequest();
        $response = $this->client->getAcsResponse($request);
        $this->assertTrue(isset($response->AccountId));
        $this->assertTrue(isset($response->Arn));
        $this->assertTrue(isset($response->RequestId));
        $this->assertTrue(isset($response->UserId));
    }
}
