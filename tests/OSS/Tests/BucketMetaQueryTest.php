<?php
namespace OSS\Tests;
require_once __DIR__ . '/Common.php';
require_once __DIR__ . '/TestOssClientBase.php';
use OSS\Core\OssException;
use OSS\Model\MetaQuery;
use OSS\Model\MetaQueryAggregation;
use OSS\Tests\TestOssClientBase;

class BucketMetaQueryTest extends TestOssClientBase
{
    protected function setUp(): void
    {
        parent::setUp();
        //folder
        for ($i = 0; $i < 12; $i++) {
            $key = 'folder/' . sprintf("%02d", $i);
            $this->ossClient->putObject($this->bucket, $key, "content");
        }
    }

    public function testMetaQuery()
    {
        try {
            $this->ossClient->openMetaQuery($this->bucket);
            $this->assertTrue(true);
        } catch (OssException $e) {
            printf("Open Meta Query status error:".$e->getMessage());
            $this->assertTrue(false);
        }

        try {
            $rs = $this->ossClient->getMetaQueryStatus($this->bucket);
            $this->assertEquals(true, $rs->getState() != "");
            $this->assertEquals(true, $rs->getUpdateTime() != "");
            $this->assertEquals(true, $rs->getCreateTime() != "");
        } catch (OssException $e) {
            printf("Get Meta Query status error:".$e->getMessage());
            $this->assertEquals(false);
        }


        sleep(180);

        try {
            $query = '{"Field": "Size","Value": "5","Operation": "gt"}';
            $metaQuery = new MetaQuery();
            $metaQuery->setQuery($query);
            $metaQuery->setMaxResults(5);
            $metaQuery->setOrder("asc");
            $metaQuery->setSort("Size");

            $rs = $this->ossClient->doMetaQuery($this->bucket,$metaQuery);
            $this->assertEquals(true,$rs->getNextToken() != null);
            $this->assertEquals(5,count($rs->getFiles()));
            $this->assertNull($rs->getAggregations());
        } catch (OssException $e) {
            printf("Get Meta Query status error:".$e->getMessage());
            $this->assertEquals(false);
        }

        try {
            $query = '{"Field": "Size","Value": "5","Operation": "gt"}';
            $metaQuery = new MetaQuery();
            $metaQuery->setQuery($query);
            $metaQuery->setMaxResults(5);
            $metaQuery->setOrder("asc");
            $metaQuery->setSort("Size");
            $agg = new MetaQueryAggregation();
            $agg->setField("Size");
            $agg->setOperation("sum");
            $aggOne = new MetaQueryAggregation();
            $aggOne->setField("Size");
            $aggOne->setOperation("count");
            $metaQuery->addAggregation($agg);
            $metaQuery->addAggregation($aggOne);
            $rs = $this->ossClient->doMetaQuery($this->bucket,$metaQuery);
            $this->assertEquals("",$rs->getNextToken());
            $this->assertNull($rs->getFiles());
            $this->assertNotNull(true,$rs->getAggregations());
        } catch (OssException $e) {
            printf("Get Meta Query status error:".$e->getMessage());
            $this->assertEquals(false);
        }


        try {
            $this->ossClient->closeMetaQuery($this->bucket);
            $this->assertTrue(true);
        } catch (OssException $e) {
            printf("Close Meta Query error:".$e->getMessage());
            $this->assertTrue(false);
        }
    }

}
