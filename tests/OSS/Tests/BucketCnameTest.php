<?php

namespace OSS\Tests;

require_once __DIR__ . '/Common.php';

use OSS\OssClient;
use OSS\Model\CnameConfig;
use OSS\Core\OssException;

class BucketCnameTest extends \PHPUnit_Framework_TestCase
{
    private $bucketName;
    private $client;

    public function setUpBeforeClass()
    {
        $this->client = Common::getOssClient();
    }

    public function setUp()
    {
        $this->bucketName = 'php-sdk-test-bucket-' . strval(rand(0, 10));
        $this->client->createBucket($this->bucketName);
    }

    public function tearDown()
    {
        $this->client->deleteBucket($this->bucketName);
    }

    public function testBucketWithoutCname()
    {
        $cnameConfig = $this->client->getBucketCname($this->bucketCname);
        $this->assertEquals(0, count($cnameConfig->getCnames());
    }

    public function testAddCname()
    {
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname('foo.com');
        $cnameConfig->addCname('www.bar.com');

        $this->client->addBucketCname($this->bucketName, $cnameConfig);

        $ret = $this->client->getBucketCname($this->bucketName);
        $this->assertEquals(2, $ret->getCnames());

        // add another 2 cnames
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname('hello.com');
        $cnameConfig->addCname('www.world.com');

        $this->client->addBucketCname($this->bucketName, $cnameConfig);

        $ret = $this->client->getBucketCname($this->bucketName);
        $this->assertEquals(4, $ret->getCnames());
    }

    public function testDeleteCname()
    {
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname('foo.com');
        $cnameConfig->addCname('www.bar.com');

        $this->client->addBucketCname($this->bucketName, $cnameConfig);

        $ret = $this->client->getBucketCname($this->bucketName);
        $this->assertEquals(2, $ret->getCnames());

        // delete one cname
        $cnameConfig = new CnameConfig();
        $cnameConfig->addCname('foo.com');

        $this->client->deleteBucketCname($this->bucketName, $cnameConfig);

        $ret = $this->client->getBucketCname($this->bucketName);
        $this->assertEquals(1, $ret->getCnames());
    }
}
