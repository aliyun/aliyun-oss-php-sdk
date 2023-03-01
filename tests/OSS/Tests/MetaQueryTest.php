<?php
namespace OSS\Tests;
require_once __DIR__ . '/Common.php';
use OSS\Core\OssException;
use OSS\Http\ResponseCore;
use OSS\Model\MetaQuery;
use OSS\Model\MetaQueryAggregation;
use OSS\Result\DoMetaQueryResult;

class MetaQueryTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBB
<?xml version="1.0" encoding="utf-8"?>
<MetaQuery>
<NextToken>MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpwZw==</NextToken>
<MaxResults>5</MaxResults>
<Query>{"Field": "Size","Value": "1048576","Operation": "gt"}</Query>
<Sort>Size</Sort>
<Order>asc</Order>
<Aggregations>
<Aggregation>
<Field>Size</Field>
<Operation>sum</Operation>
</Aggregation>
<Aggregation>
<Field>Size</Field>
<Operation>max</Operation>
</Aggregation>
</Aggregations>
</MetaQuery>
BBB;

    private $validXml1 = <<<BBB
<?xml version="1.0" encoding="utf-8"?>
<MetaQuery>
<MaxResults>5</MaxResults>
<Query>{"Field": "Size","Value": "1048576","Operation": "gt"}</Query>
<Sort>Size</Sort>
<Order>desc</Order>
<Aggregations>
<Aggregation>
<Field>Size</Field>
<Operation>group</Operation>
</Aggregation>
<Aggregation>
<Field>Size</Field>
<Operation>max</Operation>
</Aggregation>
</Aggregations>
</MetaQuery>
BBB;


    public function testValidXml(){
        $query = '{"Field": "Size","Value": "1048576","Operation": "gt"}';
        $metaQuery = new MetaQuery();
        $metaQuery->setQuery($query);
        $metaQuery->setMaxResults(5);
        $metaQuery->setSort("Size");
        $metaQuery->setOrder("asc");
        $agg = new MetaQueryAggregation();
        $agg->setField("Size");
        $agg->setOperation("sum");
        $metaQuery->addAggregation($agg);

        $aggTwo = new MetaQueryAggregation();
        $aggTwo->setField("Size");
        $aggTwo->setOperation("max");
        $metaQuery->addAggregation($aggTwo);

        $metaQuery->setNextToken("MTIzNDU2Nzg6aW1tdGVzdDpleGFtcGxlYnVja2V0OmRhdGFzZXQwMDE6b3NzOi8vZXhhbXBsZWJ1Y2tldC9zYW1wbGVvYmplY3QxLmpwZw==");
        $this->assertEquals($this->cleanXml($this->validXml),$this->cleanXml($metaQuery->serializeToXml()));
    }

    public function testValidXml1(){
        $query = '{"Field": "Size","Value": "1048576","Operation": "gt"}';
        $metaQuery = new MetaQuery();
        $metaQuery->setQuery($query);
        $metaQuery->setMaxResults(5);
        $metaQuery->setSort("Size");
        $metaQuery->setOrder("desc");
        $agg = new MetaQueryAggregation();
        $agg->setField("Size");
        $agg->setOperation("group");
        $metaQuery->addAggregation($agg);

        $aggTwo = new MetaQueryAggregation();
        $aggTwo->setField("Size");
        $aggTwo->setOperation("max");
        $metaQuery->addAggregation($aggTwo);

        $this->assertEquals($this->cleanXml($this->validXml1),$this->cleanXml($metaQuery->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }


}
