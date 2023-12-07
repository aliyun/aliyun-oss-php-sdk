<?php

namespace OSS\Tests;

use OSS\Model\ReplicationLocation;
use OSS\Model\ReplicationProgress;

class ReplicationProcessTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationProgress>
<Rule>
<ID>test_replication_1</ID>
<PrefixSet>
<Prefix>source_image</Prefix>
<Prefix>video</Prefix>
</PrefixSet>
<Action>PUT</Action>
<Destination>
<Bucket>target-bucket</Bucket>
<Location>oss-cn-beijing</Location>
<TransferType>oss_acc</TransferType>
</Destination>
<Status>doing</Status>
<HistoricalObjectReplication>enabled</HistoricalObjectReplication>
<Progress>
<HistoricalObject>0.85</HistoricalObject>
<NewObject>2015-09-24T15:28:14.000Z </NewObject>
</Progress>
</Rule>
</ReplicationProgress>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationProgress></ReplicationProgress>
BBBB;

    public function testValidXmlDemo()
    {
        $replicationProgress = new ReplicationProgress();
        $replicationProgress->parseFromXml($this->cleanXml($this->validXml));
        $this->assertEquals($this->cleanXml($replicationProgress->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $replicationProgress = new ReplicationProgress();
        $replicationProgress->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($replicationProgress->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
