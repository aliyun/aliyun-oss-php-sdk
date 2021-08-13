<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Model\LifecycleAction;
use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleRule;

class LifecycleConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validLifecycle = <<<BBBB
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
BBBB;

    private $validLifecycle2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule><ID>delete temporary files</ID>
<Prefix>temporary/</Prefix>
<Status>Enabled</Status>
<Expiration><Date>2022-10-12T00:00:00.000Z</Date></Expiration>
<Expiration2><Date>2022-10-12T00:00:00.000Z</Date></Expiration2>
</Rule>
</LifecycleConfiguration>
BBBB;
    private $validLifecycle3 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>delete objects and parts after one day</ID>
<Prefix>logs/</Prefix>
<Status>Enabled</Status>
<Expiration>
<Days>1</Days>
</Expiration>
<AbortMultipartUpload>
<Days>1</Days>
</AbortMultipartUpload>
</Rule>
<Rule>
<ID>transit objects to IA after 30, to Archive 60, expire after 10 years</ID>
<Prefix>data/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>30</Days>
<StorageClass>IA</StorageClass>
</Transition>
<Transition>
<Days>60</Days>
<StorageClass>Archive</StorageClass>
</Transition>
<Expiration>
<Days>3600</Days>
</Expiration>
</Rule>
<Rule>
<ID>transit objects to Archive after 60 days</ID>
<Prefix>important/</Prefix>
<Status>Enabled</Status>
<Transition>
<Days>6</Days>
<StorageClass>Archive</StorageClass>
</Transition>
</Rule>
<Rule>
<ID>delete created before date</ID>
<Prefix>backup/</Prefix>
<Status>Enabled</Status>
<Expiration>
<CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate>
</Expiration>
<AbortMultipartUpload>
<CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate>
</AbortMultipartUpload>
</Rule>
<Rule>
<ID>r1</ID>
<Prefix>rule1</Prefix>
<Status>Enabled</Status>
<Tag><Key>xx</Key><Value>1</Value></Tag>
<Tag><Key>yy</Key><Value>2</Value></Tag>
<Expiration>
<Days>30</Days>
</Expiration>
</Rule>
<Rule>
<ID>r2</ID>
<Prefix>rule2</Prefix>
<Status>Enabled</Status>
<Tag><Key>xx</Key><Value>1</Value></Tag>
<Transition>
<Days>60</Days>
<StorageClass>Archive</StorageClass>
</Transition>
</Rule>
</LifecycleConfiguration>
BBBB;

    private $nullLifecycle = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration/>
BBBB;

    public function testConstructValidConfig()
    {
        $lifecycleConfig = new LifecycleConfig();
        $actions = array();
        $actions[] = new LifecycleAction("Expiration", "Days", 3);
        $lifecycleRule = new LifecycleRule("delete obsoleted files", "obsoleted/", "Enabled", $actions);
        $lifecycleConfig->addRule($lifecycleRule);
        $actions = array();
        $actions[] = new LifecycleAction("Expiration", "Date", '2022-10-12T00:00:00.000Z');
        $actions[] = new LifecycleAction("Expiration2", "Date", '2022-10-12T00:00:00.000Z');
        $lifecycleRule = new LifecycleRule("delete temporary files", "temporary/", "Enabled", $actions);
        $lifecycleConfig->addRule($lifecycleRule);
        try {
            $lifecycleConfig->addRule(null);
            $this->assertFalse(true);
        } catch (OssException $e) {
            $this->assertEquals('lifecycleRule is null', $e->getMessage());
        }
        $this->assertEquals($this->cleanXml(strval($lifecycleConfig)), $this->cleanXml($this->validLifecycle));
    }

    public function testParseValidXml()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleConfig->parseFromXml($this->validLifecycle);
        $this->assertEquals($this->cleanXml($lifecycleConfig->serializeToXml()), $this->cleanXml($this->validLifecycle));
        $this->assertEquals(2, count($lifecycleConfig->getRules()));
        $rules = $lifecycleConfig->getRules();
        $this->assertEquals('delete temporary files', $rules[1]->getId());
    }

    public function testParseValidXml2()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleConfig->parseFromXml($this->validLifecycle2);
        $this->assertEquals($this->cleanXml($lifecycleConfig->serializeToXml()), $this->cleanXml($this->validLifecycle2));
        $this->assertEquals(1, count($lifecycleConfig->getRules()));
        $rules = $lifecycleConfig->getRules();
        $this->assertEquals('delete temporary files', $rules[0]->getId());
    }

    public function testParseValidXml3()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleConfig->parseFromXml($this->validLifecycle3);
        $this->assertEquals($this->cleanXml($lifecycleConfig->serializeToXml()), $this->cleanXml($this->validLifecycle3));
        $this->assertEquals(6, count($lifecycleConfig->getRules()));
        $rules = $lifecycleConfig->getRules();
        $this->assertEquals('r2', $rules[5]->getId());
    }

    public function testParseNullXml()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleConfig->parseFromXml($this->nullLifecycle);
        $this->assertEquals($this->cleanXml($lifecycleConfig->serializeToXml()), $this->cleanXml($this->nullLifecycle));
        $this->assertEquals(0, count($lifecycleConfig->getRules()));
    }

    public function testLifecycleRule()
    {
        $lifecycleRule = new LifecycleRule("x", "x", "x", array('x'));
        $lifecycleRule->setId("id");
        $lifecycleRule->setPrefix("prefix");
        $lifecycleRule->setStatus("Enabled");
        $lifecycleRule->setActions(array());

        $this->assertEquals('id', $lifecycleRule->getId());
        $this->assertEquals('prefix', $lifecycleRule->getPrefix());
        $this->assertEquals('Enabled', $lifecycleRule->getStatus());
        $this->assertEmpty($lifecycleRule->getActions());
    }

    public function testLifecycleAction()
    {
        $action = new LifecycleAction('x', 'x', 'x');
        $this->assertEquals($action->getAction(), 'x');
        $this->assertEquals($action->getTimeSpec(), 'x');
        $this->assertEquals($action->getTimeValue(), 'x');
        $action->setAction('y');
        $action->setTimeSpec('y');
        $action->setTimeValue('y');
        $this->assertEquals($action->getAction(), 'y');
        $this->assertEquals($action->getTimeSpec(), 'y');
        $this->assertEquals($action->getTimeValue(), 'y');
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
