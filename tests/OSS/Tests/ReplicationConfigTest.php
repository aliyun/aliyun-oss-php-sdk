<?php

namespace OSS\Tests;

use OSS\Model\ReplicationConfig;
use OSS\Model\ReplicationDestination;
use OSS\Model\ReplicationRule;

class ReplicationConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationConfiguration>
<Rule>
<PrefixSet>
<Prefix>prefix_1</Prefix>
<Prefix>prefix_2</Prefix>
</PrefixSet>
<Action>ALL</Action>
<Destination>
<Bucket>Target Bucket Name</Bucket>
<Location>oss-cn-hangzhou</Location>
<TransferType>oss_acc</TransferType>
</Destination>
<HistoricalObjectReplication>enabled</HistoricalObjectReplication>
</Rule>
</ReplicationConfiguration>
BBBB;
    private $validXmlOne = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationConfiguration>
<Rule>
<ID>159f80c1-a51e-4264-b832-*******</ID>
<PrefixSet>
<Prefix>prefix_5</Prefix>
<Prefix>prefix_6</Prefix>
</PrefixSet>
<Action>PUT</Action>
<Destination>
<Bucket>test-bucket</Bucket>
<Location>oss-cn-hongkong</Location>
<TransferType>oss_acc</TransferType>
</Destination>
<Status>doing</Status>
<HistoricalObjectReplication>enabled</HistoricalObjectReplication>
<SourceSelectionCriteria>
<SseKmsEncryptedObjects>
<Status>Disabled</Status>
</SseKmsEncryptedObjects>
</SourceSelectionCriteria>
<SyncRole>aliyunramrole</SyncRole>
</Rule>
<Rule>
<ID>8d66bf62-099f-48e9-9b03-*******</ID>
<PrefixSet>
<Prefix>prefix_5</Prefix>
<Prefix>prefix_6</Prefix>
</PrefixSet>
<Action>PUT</Action>
<Destination>
<Bucket>test-bucket-1</Bucket>
<Location>oss-cn-hangzhou</Location>
</Destination>
<Status>doing</Status>
<HistoricalObjectReplication>enabled</HistoricalObjectReplication>
<SourceSelectionCriteria>
<SseKmsEncryptedObjects>
<Status>Enabled</Status>
</SseKmsEncryptedObjects>
</SourceSelectionCriteria>
<SyncRole>aliyunramrole</SyncRole>
<EncryptionConfiguration>
<ReplicaKmsKeyID>c4d49f85-ee30-426b-a5ed-95e9139d****</ReplicaKmsKeyID>
</EncryptionConfiguration>
</Rule>
</ReplicationConfiguration>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationConfiguration><Rule/></ReplicationConfiguration>
BBBB;

    public function testValidXmlDemo()
    {
        $replicationConfig = new ReplicationConfig();
        $replicationConfig->parseFromXml($this->cleanXml($this->validXmlOne));
        $this->assertEquals($this->cleanXml($replicationConfig->serializeToXml()), $this->cleanXml($this->validXmlOne));
    }
    public function testValidXml()
    {
        $replicationConfig = new ReplicationConfig();
        $replicationRule = new ReplicationRule();
        $replicationRule->setPrefixSet('prefix_1');
        $replicationRule->setPrefixSet('prefix_2');
        $replicationRule->setAction('ALL');
        $replicationOssBucketDestination = new ReplicationDestination();
        $replicationOssBucketDestination->setBucket('Target Bucket Name');
        $replicationOssBucketDestination->setLocation('oss-cn-hangzhou');
        $replicationOssBucketDestination->setTransferType('oss_acc');
        $replicationRule->addDestination($replicationOssBucketDestination);
        $replicationRule->setHistoricalObjectReplication('enabled');
        $replicationConfig->addRule($replicationRule);
        $this->assertEquals($this->cleanXml($replicationConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXmlOne()
    {
        $replicationConfig = new ReplicationConfig();
        $replicationConfig->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($replicationConfig->serializeToXml()));
    }

    public function testValidXmlOne()
    {
        $replicationConfig = new ReplicationConfig();

        $replicationConfig->parseFromXml($this->cleanXml($this->validXmlOne));

        $this->assertEquals($this->cleanXml($replicationConfig->serializeToXml()), $this->cleanXml($this->validXmlOne));
    }

    public function testValidXmlTwo()
    {
        $replicationConfig = new ReplicationConfig();

        $replicationConfig->parseFromXml($this->cleanXml($this->validXml));

        $this->assertEquals($this->cleanXml($replicationConfig->serializeToXml()), $this->cleanXml($this->validXml));
    }


    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
