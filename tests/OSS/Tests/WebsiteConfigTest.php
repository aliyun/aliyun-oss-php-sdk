<?php

namespace OSS\Tests;


use OSS\Model\WebsiteConfig;
use OSS\OssClient;

class WebsiteConfigTest extends \PHPUnit_Framework_TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
<IndexDocument>
<Suffix>index.html</Suffix>
</IndexDocument>
<ErrorDocument>
<Key>errorDocument.html</Key>
</ErrorDocument>
</WebsiteConfiguration>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?><WebsiteConfiguration><IndexDocument><Suffix/></IndexDocument><ErrorDocument><Key/></ErrorDocument></WebsiteConfiguration>
BBBB;
    private $nullXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?><WebsiteConfiguration><IndexDocument><Suffix></Suffix></IndexDocument><ErrorDocument><Key></Key></ErrorDocument></WebsiteConfiguration>
BBBB;

    public function testParseValidXml()
    {
        $websiteConfig = new WebsiteConfig("index");
        $websiteConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testParsenullXml()
    {
        $websiteConfig = new WebsiteConfig();
        $websiteConfig->parseFromXml($this->nullXml);
        $this->assertTrue($this->cleanXml($this->nullXml) === $this->cleanXml($websiteConfig->serializeToXml()) ||
            $this->cleanXml($this->nullXml2) === $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testWebsiteConstruct()
    {
        $websiteConfig = new WebsiteConfig("index.html", "errorDocument.html");
        $this->assertEquals('index.html', $websiteConfig->getIndexDocument());
        $this->assertEquals('errorDocument.html', $websiteConfig->getErrorDocument());
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

    public static function tearDownAfterClass()
    {
        $accessKeyId = ' ' . getenv('OSS_ACCESS_KEY_ID') . ' ';
        $accessKeySecret = ' ' . getenv('OSS_ACCESS_KEY_SECRET') . ' ';
        $endpoint = ' ' . getenv('OSS_ENDPOINT') . '/ ';
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint, false);
        $bucket = getenv('OSS_BUCKET');

        $listObjectInfo = $ossClient->listObjects($bucket);
        $listObject = $listObjectInfo->getObjectList();
        if(count($listObject) != 0){
            foreach($listObject as $object){
                $fileName = $object->getkey();
                $ossClient->deleteObject($bucket,$fileName);
            }
        }
        $prefix = 'test/';
        $delimiter = '/';
        $nextMarker = '';
        $maxkeys = 30;
        while (true) {
            $options = array(
                'delimiter' => $delimiter,
                'prefix' => $prefix,
                'max-keys' => $maxkeys,
                'marker' => $nextMarker,
            );

            $listObjectInfo = $ossClient->listObjects($bucket, $options);

            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();

            foreach($listObject as $info){
                $file =$info->getKey();
                $ossClient->deleteObject($bucket,$file);
            }

            if ($nextMarker === '') {
                break;
            }
        }

        $ossClient->deleteObject($bucket,'test-dir/');

        $ossClient ->deleteBucket($bucket);
    }
}
