<?php
namespace OSS\Tests;
require_once __DIR__ . '/Common.php';
use OSS\Core\OssException;
use OSS\Http\ResponseCore;
use OSS\Model\MetaQuery;
use OSS\Model\MetaQueryAggregation;
use OSS\Result\DoMetaQueryResult;

class BucketDoMetaQueryResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<MetaQuery>
  <NextToken>MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpwZw==</NextToken>
  <Files>
    <File>
      <Filename>exampleobject.txt</Filename>
      <Size>120</Size>
      <FileModifiedTime>2021-06-29T14:50:13.011643661+08:00</FileModifiedTime>
      <OSSObjectType>Normal</OSSObjectType>
      <OSSStorageClass>Standard</OSSStorageClass>
      <ObjectACL>default</ObjectACL>
      <ETag>"fba9dede5f27731c9771645a3986"</ETag>
      <OSSCRC64>4858A48BD1466884</OSSCRC64>
      <OSSTaggingCount>2</OSSTaggingCount>
      <OSSTagging>
        <Tagging>
          <Key>owner</Key>
          <Value>John</Value>
        </Tagging>
        <Tagging>
          <Key>type</Key>
          <Value>document</Value>
        </Tagging>
      </OSSTagging>
      <OSSUserMeta>
        <UserMeta>
          <Key>x-oss-meta-location</Key>
          <Value>hangzhou</Value>
        </UserMeta>
      </OSSUserMeta>
    </File>
  </Files>
</MetaQuery>
BBBB;

    private $validXml2 = <<<BBB
<MetaQuery>
  <NextToken></NextToken>
  <Aggregations>
    <Aggregation>
      <Field>Size</Field>
      <Operation>sum</Operation>
      <Value>839665882</Value>
    </Aggregation>
    <Aggregation>
      <Field>Size</Field>
      <Operation>count</Operation>
      <Value>36</Value>
    </Aggregation>
    <Aggregation>
      <Field>Size</Field>
      <Operation>group</Operation>
      <Groups>
        <Group>
          <Value>518</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>581</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>605</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>793</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>858</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>1021</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>1235</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>1644</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>2222</Value>
          <Count>1</Count>
        </Group>
        <Group>
          <Value>2634</Value>
          <Count>1</Count>
        </Group>
      </Groups>
    </Aggregation>
  </Aggregations>
</MetaQuery>
BBB;


    public function testParseValidXml()
    {
        try {
            $response = new ResponseCore(array(), $this->validXml, 200);
            $result = new DoMetaQueryResult($response);
            $this->assertTrue($result->isOK());
            $this->assertNotNull($result->getData());
            $this->assertNotNull($result->getRawResponse());
            $queryResult = $result->getData();
            $this->assertEquals("MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpwZw==",$queryResult->getNextToken());
            $file = $queryResult->getFiles()[0];
            $this->assertEquals("exampleobject.txt",$file->getFileName());
            $this->assertEquals(120,$file->getSize());
            $this->assertEquals("2021-06-29T14:50:13.011643661+08:00",$file->getFileModifiedTime());
            $this->assertEquals("Normal",$file->getOssObjectType());
            $this->assertEquals("Standard",$file->getOssStorageClass());
            $this->assertEquals("default",$file->getObjectAcl());
            $this->assertEquals("\"fba9dede5f27731c9771645a3986\"",$file->getETag());
            $this->assertEquals("4858A48BD1466884",$file->getOssCrc64());
            $this->assertEquals(2,$file->getOssTaggingCount());
            $this->assertEquals("default",$file->getObjectAcl());
            $tag = $file->getOssTagging()[1];
            $this->assertEquals("type",$tag->getKey());
            $this->assertEquals("document",$tag->getValue());
            $userMeta = $file->getOssUserMeta()[0];
            $this->assertEquals("x-oss-meta-location",$userMeta->getKey());
            $this->assertEquals("hangzhou",$userMeta->getValue());
        }catch (OssException $e){
            print_r($e->getMessage());
            $this->assertEquals(false);
        }
    }

    public function testValidXml2()
    {
        try {
            $response = new ResponseCore(array(), $this->validXml2, 200);
            $result = new DoMetaQueryResult($response);
            $this->assertTrue($result->isOK());
            $this->assertNotNull($result->getData());
            $this->assertNotNull($result->getRawResponse());
            $queryResult = $result->getData();
            $this->assertEquals("",$queryResult->getNextToken());
            $files = $queryResult->getFiles();
            $this->assertNull($files);
            $agg = $queryResult->getAggregations()[0];
            $this->assertEquals("Size",$agg->getField());
            $this->assertEquals("sum",$agg->getOperation());
            $this->assertEquals("839665882",$agg->getValue());
            $agg3 = $queryResult->getAggregations()[2];
            $this->assertEquals("group",$agg3->getOperation());

            $group2 = $agg3->getGroups()[2];
            $this->assertEquals("605",$group2->getValue());
            $this->assertEquals(1,$group2->getCount());
        }catch (OssException $e){
            print_r($e->getMessage());
            $this->assertEquals(false);
        }
    }

}
