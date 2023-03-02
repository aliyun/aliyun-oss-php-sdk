<?php

namespace OSS\Tests;

use OSS\Model\ReplicationConfig;
use OSS\Model\ReplicationDestination;
use OSS\Model\ReplicationLocation;
use OSS\Model\ReplicationRule;

class ReplicationLocationTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationLocation>
<Location>oss-cn-beijing</Location>
<Location>oss-cn-qingdao</Location>
<Location>oss-cn-shenzhen</Location>
<Location>oss-cn-hongkong</Location>
<Location>oss-us-west-1</Location>
<LocationTransferTypeConstraint>
<LocationTransferType>
<Location>oss-cn-hongkong</Location>
<TransferTypes>
<Type>oss_acc</Type>
</TransferTypes>
</LocationTransferType>
<LocationTransferType>
<Location>oss-us-west-1</Location>
<TransferTypes>
<Type>oss_acc</Type>
</TransferTypes>
</LocationTransferType>
</LocationTransferTypeConstraint>
</ReplicationLocation>
BBBB;
    private $invalidXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ReplicationLocation></ReplicationLocation>
BBBB;

    public function testValidXmlDemo()
    {
        $replicationLocation = new ReplicationLocation();
        $replicationLocation->parseFromXml($this->cleanXml($this->validXml));
        $this->assertEquals($this->cleanXml($replicationLocation->serializeToXml()), $this->cleanXml($this->validXml));
    }

    public function testInvalidXml()
    {
        $replicationLocation = new ReplicationLocation();
        $replicationLocation->parseFromXml($this->cleanXml($this->invalidXml));
        $this->assertEquals($this->cleanXml($this->invalidXml), $this->cleanXml($replicationLocation->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
