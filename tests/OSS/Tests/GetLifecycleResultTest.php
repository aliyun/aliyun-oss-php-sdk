<?php

namespace OSS\Tests;


use OSS\Http\ResponseCore;
use OSS\Core\OssException;
use OSS\Model\LifecycleConfig;
use OSS\Result\GetLifecycleResult;
use OSS\Result\Result;

class GetLifecycleResultTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>delete obsoleted files</ID>
<Prefix>obsoleted/</Prefix>
<Status>Enabled</Status>
<Expiration><Days>3</Days></Expiration>
</Rule>
<Rule>
<ID>delete temporary files</ID>
<Prefix>temporary/</Prefix>
<Status>Enabled</Status>
<Expiration><Date>2022-10-12T00:00:00.000Z</Date></Expiration>
</Rule>
</LifecycleConfiguration>
BBBB;
    private $validXml1 = <<<BBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule><ID>rule1</ID>
<Prefix>logs/</Prefix>
<Status>Enabled</Status>
<Expiration><Days>3</Days></Expiration>
<AbortMultipartUpload><Days>1</Days></AbortMultipartUpload>
</Rule>
<Rule>
<ID>rule2</ID>
<Prefix>logs2/</Prefix>
<Status>Enabled</Status>
<Expiration><Days>180</Days></Expiration>
<Transition><Days>30</Days><StorageClass>IA</StorageClass></Transition>
<Transition><Days>60</Days><StorageClass>Archive</StorageClass></Transition>
</Rule><Rule><ID>rule3</ID><Prefix>logs3/</Prefix><Status>Enabled</Status>
<Expiration><CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate></Expiration>
<AbortMultipartUpload><CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate></AbortMultipartUpload>
</Rule><Rule><ID>rule4</ID><Prefix>logs4/</Prefix><Status>Enabled</Status><Tag><Key>key1</Key><Value>val1</Value></Tag><Tag><Key>key12</Key><Value>val12</Value></Tag><Transition><Days>30</Days><StorageClass>IA</StorageClass></Transition></Rule><Rule><ID>rule5</ID><Prefix>logs5/</Prefix><Status>Enabled</Status><Transition><Days>30</Days><StorageClass>IA</StorageClass><IsAccessTime>false</IsAccessTime></Transition></Rule><Rule><ID>rule6</ID><Prefix>logs6/</Prefix><Status>Enabled</Status><Transition><Days>30</Days><StorageClass>IA</StorageClass><IsAccessTime>true</IsAccessTime><ReturnToStdWhenVisit>false</ReturnToStdWhenVisit></Transition></Rule><Rule><ID>rule7</ID><Prefix>logs7/</Prefix><Status>Enabled</Status><NoncurrentVersionTransition><NoncurrentDays>30</NoncurrentDays><StorageClass>IA</StorageClass><IsAccessTime>true</IsAccessTime><ReturnToStdWhenVisit>true</ReturnToStdWhenVisit></NoncurrentVersionTransition></Rule></LifecycleConfiguration>
BBB;

    private $validXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>RuleID</ID>
<Prefix>logs</Prefix>
<Status>Enabled</Status>
<Expiration>
<Days>100</Days>
</Expiration>
<Transition>
<Days>Days</Days>
<StorageClass>Archive</StorageClass>
</Transition>
<Filter>
<Not>
<Prefix>logs1</Prefix>
<Tag><Key>key1</Key><Value>value1</Value></Tag>
</Not>
</Filter>
</Rule>
</LifecycleConfiguration>
BBBB;

    private $inValidXml = <<<AAA
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>delete obsoleted files</ID>
<Prefix>obsoleted/</Prefix>
<Status>Enabled</Status>
<Expiration><Days>3</Days></Expiration>
</Rule>
<Rule>
<ID>delete temporary files</ID>
<Prefix>temporary/</Prefix>
<Status>Enabled</Status>
<Expiration><Date>2022-10-12T00:00:00.000Z</Date></Expiration>
<Expiration2><Date>2022-10-12T00:00:00.000Z</Date></Expiration2>
</Rule>
</LifecycleConfiguration>
AAA;

    private $validXml3 = <<<AAA
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>rule1</ID>
<Prefix>logs/</Prefix>
<Status>Enabled</Status>
<Expiration>
<ExpiredObjectDeleteMarker>true</ExpiredObjectDeleteMarker>
</Expiration>
<AbortMultipartUpload>
<Days>1</Days>
</AbortMultipartUpload>
<NoncurrentVersionExpiration>
<NoncurrentDays>5</NoncurrentDays>
</NoncurrentVersionExpiration>
</Rule>
<Rule>
<ID>rule2</ID>
<Prefix>data/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>30</Days>
<StorageClass>IA</StorageClass>
</Transition>
<NoncurrentVersionTransition>
<NoncurrentDays>10</NoncurrentDays>
<StorageClass>IA</StorageClass>
</NoncurrentVersionTransition>
</Rule>
<Rule>
<ID>rule3</ID>
<Prefix>logs5/</Prefix>
<Status>Enabled</Status>
<NoncurrentVersionTransition>
<NoncurrentDays>10</NoncurrentDays>
<StorageClass>IA</StorageClass>
<IsAccessTime>true</IsAccessTime>
<ReturnToStdWhenVisit>false</ReturnToStdWhenVisit>
</NoncurrentVersionTransition>
</Rule>
</LifecycleConfiguration>
AAA;




    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetLifecycleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $lifecycleConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($lifecycleConfig->serializeToXml()));
    }

    public function testParseValidXml2()
    {
        $response = new ResponseCore(array(), $this->validXml2, 200);
        $result = new GetLifecycleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $lifecycleConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml2), $this->cleanXml($lifecycleConfig->serializeToXml()));
    }

    public function testParseValidXml3()
    {
        $response = new ResponseCore(array(), $this->validXml3, 200);
        $result = new GetLifecycleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $lifecycleConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml3), $this->cleanXml($lifecycleConfig->serializeToXml()));
    }

    public function testInValidXml()
    {
        $response = new ResponseCore(array(), $this->inValidXml, 200);
        try {
            $result = new GetLifecycleResult($response);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(false);
        }
    }

    public function testParseValidXmlOne()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new GetLifecycleResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $lifecycleConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($lifecycleConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

    public function testInvalidResponse()
    {
        $response = new ResponseCore(array(), $this->validXml, 300);
        try {
            $result = new GetLifecycleResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            printf($e->getMessage());
            $this->assertTrue(true);
        }
    }
}
