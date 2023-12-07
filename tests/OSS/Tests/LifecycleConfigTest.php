<?php

namespace OSS\Tests;

use OSS\Model\LifecycleConfig;
use OSS\Model\LifecycleNoncurrentVersionExpiration;
use OSS\Model\LifecycleRule;
use OSS\OssClient;
use OSS\Model\LifecycleExpiration;
use OSS\Model\LifecycleAbortMultipartUpload;
use OSS\Model\LifecycleTag;
use OSS\Model\LifecycleTransition;
use OSS\Model\LifecycleNoncurrentVersionTransition;
use \OSS\Model\LifecycleNot;
use \OSS\Model\LifecycleFilter;

class LifecycleConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validLifecycle = <<<BBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration><Rule><ID>rule1</ID><Prefix>logs/</Prefix><Status>Enabled</Status><Expiration><Days>3</Days></Expiration><AbortMultipartUpload><Days>1</Days></AbortMultipartUpload></Rule><Rule><ID>rule2</ID><Prefix>logs2/</Prefix><Status>Enabled</Status><Expiration><Days>180</Days></Expiration><Transition><Days>30</Days><StorageClass>IA</StorageClass></Transition><Transition><Days>60</Days><StorageClass>Archive</StorageClass></Transition></Rule><Rule><ID>rule3</ID><Prefix>logs3/</Prefix><Status>Enabled</Status><Expiration><CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate></Expiration><AbortMultipartUpload><CreatedBeforeDate>2017-01-01T00:00:00.000Z</CreatedBeforeDate></AbortMultipartUpload></Rule><Rule><ID>rule4</ID><Prefix>logs4/</Prefix><Status>Enabled</Status><Tag><Key>key1</Key><Value>val1</Value></Tag><Tag><Key>key12</Key><Value>val12</Value></Tag><Transition><Days>30</Days><StorageClass>IA</StorageClass></Transition></Rule><Rule><ID>rule5</ID><Prefix>logs5/</Prefix><Status>Enabled</Status><Transition><Days>30</Days><StorageClass>IA</StorageClass><IsAccessTime>false</IsAccessTime></Transition></Rule><Rule><ID>rule6</ID><Prefix>logs6/</Prefix><Status>Enabled</Status><Transition><Days>30</Days><StorageClass>IA</StorageClass><IsAccessTime>true</IsAccessTime><ReturnToStdWhenVisit>false</ReturnToStdWhenVisit></Transition></Rule><Rule><ID>rule7</ID><Prefix>logs7/</Prefix><Status>Enabled</Status><NoncurrentVersionTransition><NoncurrentDays>30</NoncurrentDays><StorageClass>IA</StorageClass><IsAccessTime>true</IsAccessTime><ReturnToStdWhenVisit>true</ReturnToStdWhenVisit></NoncurrentVersionTransition></Rule></LifecycleConfiguration>

BBB;

    private $validLifecycle2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule><ID>delete temporary files</ID>
<Prefix>temporary/</Prefix>
<Status>Enabled</Status>
<Expiration><Date>2022-10-12T00:00:00.000Z</Date></Expiration>
</Rule>
</LifecycleConfiguration>
BBBB;

    private $validLifecycle3 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration>
<Rule>
<ID>r1</ID>
<Prefix>abc/</Prefix>
<Filter>
<ObjectSizeGreaterThan>500</ObjectSizeGreaterThan>
<ObjectSizeLessThan>64000</ObjectSizeLessThan>
<Not>
<Prefix>abc/not1/</Prefix>
<Tag>
<Key>notkey1</Key>
<Value>notvalue1</Value>
</Tag>
</Not>
<Not>
<Prefix>abc/not2/</Prefix>
<Tag>
<Key>notkey2</Key>
<Value>notvalue2</Value>
</Tag>
</Not>
</Filter>
</Rule>
<Rule>
<ID>r2</ID>
<Prefix>def/</Prefix>
<Filter>
<ObjectSizeGreaterThan>500</ObjectSizeGreaterThan>
<Not>
<Prefix>def/not1/</Prefix>
</Not>
<Not>
<Prefix>def/not2/</Prefix>
<Tag>
<Key>notkey2</Key>
<Value>notvalue2</Value>
</Tag>
</Not>
</Filter>
</Rule>
</LifecycleConfiguration>
BBBB;

    private $nullLifecycle = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<LifecycleConfiguration/>
BBBB;




    public function testConstructValidConfig()
    {$lifecycleConfig = new LifecycleConfig();

        $rule1 = new LifecycleRule("rule1", "logs/", LifecycleRule::STATUS_ENANLED);
        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setDays(3);
        $rule1->setExpiration($lifecycleExpiration);

        $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
        $lifecycleAbortMultipartUpload->setDays(1);
        $rule1->setAbortMultipartUpload($lifecycleAbortMultipartUpload);

        $lifecycleConfig->addRule($rule1);

        $rule2 = new LifecycleRule("rule2", "logs2/", LifecycleRule::STATUS_ENANLED);
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $rule2->addTransition($lifecycleTransition);
        // 60 天 转换Object的存储类型为 Archive
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(60);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_ARCHIVE);
        $rule2->addTransition($lifecycleTransition);

        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setDays(180);
        $rule2->setExpiration($lifecycleExpiration);
        $lifecycleConfig->addRule($rule2);

        $rule3 = new LifecycleRule("rule3", "logs3/", LifecycleRule::STATUS_ENANLED);
        $lifecycleExpiration = new LifecycleExpiration();
        $lifecycleExpiration->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
        $rule3->setExpiration($lifecycleExpiration);

        $lifecycleAbortMultipartUpload = new LifecycleAbortMultipartUpload();
        $lifecycleAbortMultipartUpload->setCreatedBeforeDate("2017-01-01T00:00:00.000Z");
        $rule3->setAbortMultipartUpload($lifecycleAbortMultipartUpload);
        $lifecycleConfig->addRule($rule3);

        $rule4 = new LifecycleRule("rule4", "logs4/", LifecycleRule::STATUS_ENANLED);

        $tag = new LifecycleTag();
        $tag->setKey("key1");
        $tag->setValue("val1");
        $rule4->addTag($tag);

        $tag2 = new LifecycleTag();
        $tag2->setKey("key12");
        $tag2->setValue("val12");
        $rule4->addTag($tag2);
        $lifecycleConfig->addRule($rule4);
        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $rule4->addTransition($lifecycleTransition);

        $rule5 = new LifecycleRule("rule5", "logs5/", LifecycleRule::STATUS_ENANLED);

        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $lifecycleTransition->setIsAccessTime(false);
        $rule5->addTransition($lifecycleTransition);
        $lifecycleConfig->addRule($rule5);

        $rule6 = new LifecycleRule("rule6", "logs6/", LifecycleRule::STATUS_ENANLED);

        $lifecycleTransition = new LifecycleTransition();
        $lifecycleTransition->setDays(30);
        $lifecycleTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $lifecycleTransition->setIsAccessTime(true);
        $lifecycleTransition->setReturnToStdWhenVisit(false);
        $rule6->addTransition($lifecycleTransition);
        $lifecycleConfig->addRule($rule6);

        $rule7 = new LifecycleRule("rule7", "logs7/", LifecycleRule::STATUS_ENANLED);

        $nonTransition = new LifecycleNoncurrentVersionTransition();
        $nonTransition->setNoncurrentDays(30);
        $nonTransition->setStorageClass(OssClient::OSS_STORAGE_IA);
        $nonTransition->setIsAccessTime(true);
        $nonTransition->setReturnToStdWhenVisit(true);
        $rule7->addNoncurrentVersionTransition($nonTransition);
        $lifecycleConfig->addRule($rule7);

        $this->assertEquals($this->cleanXml(strval($lifecycleConfig)), $this->cleanXml($this->validLifecycle));
    }

    public function testParseValidXml()
    {
        $lifecycleConfig = new LifecycleConfig();
        $lifecycleConfig->parseFromXml($this->validLifecycle);
        $this->assertEquals($this->cleanXml($lifecycleConfig->serializeToXml()), $this->cleanXml($this->validLifecycle));
        $this->assertEquals(7, count($lifecycleConfig->getRules()));
        $rules = $lifecycleConfig->getRules();
        $this->assertEquals('rule2', $rules[1]->getId());
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
        $this->assertEquals(2, count($lifecycleConfig->getRules()));
        $rules = $lifecycleConfig->getRules();
        $this->assertEquals('r1', $rules[0]->getId());
        $filter = $rules[0]->getFilter();
        $this->assertEquals(500, $filter->getObjectSizeGreaterThan());
        $this->assertEquals(64000, $filter->getObjectSizeLessThan());
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
        $lifecycleRule = new LifecycleRule("x", "x", "x");
        $lifecycleRule->setId("id");
        $lifecycleRule->setPrefix("prefix");
        $lifecycleRule->setStatus("Enabled");

        $this->assertEquals('id', $lifecycleRule->getId());
        $this->assertEquals('prefix', $lifecycleRule->getPrefix());
        $this->assertEquals('Enabled', $lifecycleRule->getStatus());
    }

    public function testLifecycleRuleWithFilter()
    {
        $lifecycleRule = new LifecycleRule("x", "x", "x");
        $lifecycleRule->setId("id");
        $lifecycleRule->setPrefix("prefix");
        $lifecycleRule->setStatus("Enabled");

        $not = new LifecycleNot();
        $tag = new LifecycleTag();
        $tag->setKey("key1");
        $tag->setValue("val1");
        $not->setTag($tag);
        $not->setPrefix("log1/");

        $filter = new LifecycleFilter();

        $filter->addNot($not);

        $lifecycleRule->setFilter($filter);

        $this->assertEquals('id', $lifecycleRule->getId());
        $this->assertEquals('prefix', $lifecycleRule->getPrefix());
        $this->assertEquals('Enabled', $lifecycleRule->getStatus());

        $getFilter = $lifecycleRule->getFilter();
        $getNot = $getFilter->getNot();
        $this->assertEquals($getNot[0]->getPrefix(),"log1/");
        $getFilterTag = $getNot[0]->getTag();
        $this->assertEquals($getFilterTag->getKey(),"key1");
        $this->assertEquals($getFilterTag->getValue(),"val1");


        $lifecycleRule = new LifecycleRule("x", "x", "x");
        $lifecycleRule->setId("id");
        $lifecycleRule->setPrefix("prefix");
        $lifecycleRule->setStatus("Enabled");

        $not = new LifecycleNot();

        $filter = new LifecycleFilter();

        $filter->addNot($not);

        $filter->setObjectSizeGreaterThan(599);

        $filter->setObjectSizeLessThan(899);

        $lifecycleRule->setFilter($filter);


        $this->assertEquals('id', $lifecycleRule->getId());
        $this->assertEquals('prefix', $lifecycleRule->getPrefix());
        $this->assertEquals('Enabled', $lifecycleRule->getStatus());

        $getFilter = $lifecycleRule->getFilter();
        $getNot = $getFilter->getNot();
        $this->assertEquals($getNot[0]->getPrefix(),"log1/");
        $getFilterTag = $getNot[0]->getTag();
        $this->assertEquals($getFilterTag->getKey(),"key1");
        $this->assertEquals($getFilterTag->getValue(),"val1");
    }

    public function testLifecycleTag()
    {
        $tag = new LifecycleTag('key1', 'val1');
        $this->assertEquals($tag->getKey(), 'key1');
        $this->assertEquals($tag->getValue(), 'val1');

        $tag->setKey('v1');
        $tag->setValue('v2');
        $this->assertEquals($tag->getKey(), 'v1');
        $this->assertEquals($tag->getValue(), 'v2');
    }

    public function testLifecycleTransition()
    {
        $transition= new LifecycleTransition(5, '2017-01-01T00:00:00.000Z',"IA",true,false);
        $this->assertEquals($transition->getDays(), 5);
        $this->assertEquals($transition->getCreatedBeforeDate(), '2017-01-01T00:00:00.000Z');
        $this->assertEquals($transition->getStorageClass(), 'IA');
        $this->assertEquals($transition->getIsAccessTime(), true);
        $this->assertEquals($transition->getReturnToStdWhenVisit(), false);

        $transition->setDays(10);
        $transition->setCreatedBeforeDate("2022-01-01T00:00:00.000Z");
        $transition->setStorageClass(OssClient::OSS_STORAGE_ARCHIVE);
        $transition->setIsAccessTime(false);
        $transition->setReturnToStdWhenVisit(false);

        $this->assertEquals($transition->getDays(), 10);
        $this->assertEquals($transition->getCreatedBeforeDate(), '2022-01-01T00:00:00.000Z');
        $this->assertEquals($transition->getStorageClass(), OssClient::OSS_STORAGE_ARCHIVE);
        $this->assertEquals($transition->getIsAccessTime(), false);
        $this->assertEquals($transition->getReturnToStdWhenVisit(), false);
    }

    public function testLifecycleNonTransition()
    {
        $transition= new LifecycleNoncurrentVersionTransition(5, "IA",true,false);
        $this->assertEquals($transition->getNoncurrentDays(), 5);
        $this->assertEquals($transition->getStorageClass(), 'IA');
        $this->assertEquals($transition->getIsAccessTime(), true);
        $this->assertEquals($transition->getReturnToStdWhenVisit(), false);

        $transition->setNoncurrentDays(10);
        $transition->setStorageClass(OssClient::OSS_STORAGE_ARCHIVE);
        $transition->setIsAccessTime(false);
        $transition->setReturnToStdWhenVisit(false);

        $this->assertEquals($transition->getNoncurrentDays(), 10);
        $this->assertEquals($transition->getStorageClass(), OssClient::OSS_STORAGE_ARCHIVE);
        $this->assertEquals($transition->getIsAccessTime(), false);
        $this->assertEquals($transition->getReturnToStdWhenVisit(), false);
    }

    public function testLifecycleAbortMultipartUpload()
    {
        $abortMultipartUpload= new LifecycleAbortMultipartUpload(10, '2022-01-01T00:00:00.000Z');
        $this->assertEquals($abortMultipartUpload->getDays(), 10);
        $this->assertEquals($abortMultipartUpload->getCreatedBeforeDate(), '2022-01-01T00:00:00.000Z');

        $abortMultipartUpload->setDays(1);
        $abortMultipartUpload->setCreatedBeforeDate('2021-01-01T00:00:00.000Z');
        $this->assertEquals($abortMultipartUpload->getDays(), 1);
        $this->assertEquals($abortMultipartUpload->getCreatedBeforeDate(), '2021-01-01T00:00:00.000Z');
    }


    public function testLifecycleExpiration()
    {
        $expiration= new LifecycleExpiration(10, '2022-01-01T00:00:00.000Z',"2022-02-01T00:00:00.000Z",false);
        $this->assertEquals($expiration->getDays(), 10);
        $this->assertEquals($expiration->getDate(), "2022-01-01T00:00:00.000Z");
        $this->assertEquals($expiration->getCreatedBeforeDate(), '2022-02-01T00:00:00.000Z');
        $this->assertEquals($expiration->getExpiredObjectDeleteMarker(), false);

        $expiration->setDays(1);
        $expiration->setCreatedBeforeDate('2021-01-01T00:00:00.000Z');
        $expiration->setDate("2021-01-02T00:00:00.000Z");
        $expiration->setExpiredObjectDeleteMarker(true);

        $this->assertEquals($expiration->getDays(), 1);
        $this->assertEquals($expiration->getDate(), "2021-01-02T00:00:00.000Z");
        $this->assertEquals($expiration->getCreatedBeforeDate(), '2021-01-01T00:00:00.000Z');
        $this->assertEquals($expiration->getExpiredObjectDeleteMarker(), true);
    }


    public function testLifecycleNonExpiration()
    {
        $expiration= new LifecycleNoncurrentVersionExpiration(10, '2022-01-01T00:00:00.000Z',"2022-02-01T00:00:00.000Z",false);
        $this->assertEquals($expiration->getNoncurrentDays(), 10);
        $expiration->setNoncurrentDays(1);
        $this->assertEquals($expiration->getNoncurrentDays(), 1);
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
