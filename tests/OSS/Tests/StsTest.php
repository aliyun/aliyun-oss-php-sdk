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
    }

    public function  testAssumeRole()
    {
        $request = new Sts\AssumeRoleRequest();
        $request->setRoleSessionName(self::CLIENT_NAME);
        $request->setRoleArn(getenv('OSS_STS_ARN'));
        $request->setPolicy(self::POLICY);
        $request->setDurationSeconds(self::EXPIRE_TIME);

        $iClientProfile = \DefaultProfile::getProfile(self::REGION_ID, getenv('OSS_STS_ID'), getenv('OSS_STS_KEY'));
        $this->client = new \DefaultAcsClient($iClientProfile);
        $response = $this->client->getAcsResponse($request);

        $this->assertTrue(isset($response->AssumedRoleUser));
        $this->assertTrue(isset($response->Credentials));
        $this->assertEquals($response->AssumedRoleUser->Arn, getenv('OSS_STS_ARN').'/'.self::CLIENT_NAME);
        $time = substr($response->Credentials->Expiration, 0, 10).' '.substr($response->Credentials->Expiration, 11, 8);
        $this->assertEquals(strtotime($time)-strtotime("now"),self::EXPIRE_TIME);
    }

    public function testGetCallerIdentity()
    {
        $request = new Sts\GetCallerIdentityRequest();
        $iClientProfile = \DefaultProfile::getProfile(self::REGION_ID, getenv('OSS_STS_ID'), getenv('OSS_STS_KEY'));

        $this->client = new \DefaultAcsClient($iClientProfile);
        $response = $this->client->getAcsResponse($request);

        $this->assertTrue(isset($response->AccountId));
        $this->assertTrue(isset($response->Arn));
        $this->assertTrue(isset($response->RequestId));
        $this->assertTrue(isset($response->UserId));
    }

    const REGION_ID = "cn-shanghai";
    const ENDPOINT = "sts.cn-shanghai.aliyuncs.com";
    const CLIENT_NAME = "sts";
    const EXPIRE_TIME = "3600";
    const POLICY = <<<POLICY
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

}
