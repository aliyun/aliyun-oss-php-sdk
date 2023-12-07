<?php

namespace OSS\Tests;


use OSS\Model\WebsiteCondition;
use OSS\Model\WebsiteConfig;
use OSS\Model\WebsiteIncludeHeader;
use OSS\Model\WebsiteIndexDocument;
use OSS\Model\WebsiteErrorDocument;
use OSS\Model\WebsiteMirrorHeaders;
use OSS\Model\WebsiteMirrorHeadersSet;
use OSS\Model\WebsiteRedirect;
use OSS\Model\WebsiteRoutingRule;

class WebsiteConfigTest extends \PHPUnit\Framework\TestCase
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

    private $manyXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
<IndexDocument>
<Suffix>index.html</Suffix>
</IndexDocument>
<ErrorDocument>
<Key>error.html</Key>
<HttpStatus>404</HttpStatus>
</ErrorDocument>
<RoutingRules>
<RoutingRule>
<RuleNumber>1</RuleNumber>
<Condition>
<KeyPrefixEquals>abc/</KeyPrefixEquals>
<HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
</Condition>
<Redirect>
<RedirectType>Mirror</RedirectType>
<PassQueryString>true</PassQueryString>
<MirrorURL>http://example.com/</MirrorURL>
<MirrorPassQueryString>true</MirrorPassQueryString>
<MirrorFollowRedirect>true</MirrorFollowRedirect>
<MirrorCheckMd5>false</MirrorCheckMd5>
<MirrorHeaders>
<PassAll>true</PassAll>
<Pass>myheader-key1</Pass>
<Pass>myheader-key2</Pass>
<Remove>myheader-key3</Remove>
<Remove>myheader-key4</Remove>
<Set>
<Key>myheader-key5</Key>
<Value>myheader-value5</Value>
</Set>
</MirrorHeaders>
</Redirect>
</RoutingRule>
<RoutingRule>
<RuleNumber>2</RuleNumber>
<Condition>
<KeyPrefixEquals>abc/</KeyPrefixEquals>
<HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
<IncludeHeader>
<Key>host</Key>
<Equals>test.oss-cn-beijing-internal.aliyuncs.com</Equals>
</IncludeHeader>
</Condition>
<Redirect>
<RedirectType>AliCDN</RedirectType>
<PassQueryString>false</PassQueryString>
<Protocol>http</Protocol>
<HostName>example.com</HostName>
<ReplaceKeyWith>prefix/!{key}.suffix</ReplaceKeyWith>
<HttpRedirectCode>301</HttpRedirectCode>
</Redirect>
</RoutingRule>
</RoutingRules>
</WebsiteConfiguration>
BBBB;


    private $oneRule = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
<IndexDocument>
<Suffix>index.html</Suffix>
<SupportSubDir>false</SupportSubDir>
<Type>0</Type>
</IndexDocument>
<ErrorDocument>
<Key>error.html</Key>
<HttpStatus>404</HttpStatus>
</ErrorDocument>
<RoutingRules>
<RoutingRule>
<RuleNumber>1</RuleNumber>
<Condition>
<KeyPrefixEquals>examplebucket</KeyPrefixEquals>
<HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
<IncludeHeader>
<Key>host</Key>
<Equals>test.oss-cn-beijing-internal.aliyuncs.com</Equals>
</IncludeHeader>
</Condition>
<Redirect>
<RedirectType>Mirror</RedirectType>
<PassQueryString>true</PassQueryString>
<MirrorURL>https://www.example.com/</MirrorURL>
<MirrorPassQueryString>true</MirrorPassQueryString>
<MirrorCheckMd5>true</MirrorCheckMd5>
<MirrorHeaders>
<PassAll>true</PassAll>
<Pass>cache-control-one</Pass>
<Pass>pass-one</Pass>
<Remove>remove-one</Remove>
<Remove>test-two</Remove>
<Set>
<Key>key1</Key>
<Value>value1</Value>
</Set>
</MirrorHeaders>
<EnableReplacePrefix>false</EnableReplacePrefix>
<ReplaceKeyWith>key.jpg</ReplaceKeyWith>
</Redirect>
</RoutingRule>
</RoutingRules>
</WebsiteConfiguration>
BBBB;


    public function testParseValidXml()
    {
        $websiteConfig = new WebsiteConfig("index.html","errorDocument.html");
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testParsenullXml()
    {
        $index = new WebsiteIndexDocument("");
        $error = new WebsiteErrorDocument("");
        $websiteConfig = new WebsiteConfig($index, $error);
        $this->assertTrue($this->cleanXml($this->nullXml) === $this->cleanXml($websiteConfig->serializeToXml()) ||
            $this->cleanXml($this->nullXml2) === $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testWebsiteConstruct()
    {
        $websiteConfig = new WebsiteConfig("index.html", "errorDocument.html");
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }
    public function testParseValidXmlNew()
    {
        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("error.html");
        $websiteConfig = new WebsiteConfig($index, $error);
        $websiteConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testParsenullXmlNew()
    {
        $websiteConfig = new WebsiteConfig();
        $websiteConfig->parseFromXml($this->nullXml);
        $this->assertTrue($this->cleanXml($this->nullXml) === $this->cleanXml($websiteConfig->serializeToXml()) ||
            $this->cleanXml($this->nullXml2) === $this->cleanXml($websiteConfig->serializeToXml()));
    }


    public function testParseManyRule()
    {

        $index = new WebsiteIndexDocument('index.html');
        $error = new WebsiteErrorDocument('error.html',404);
        $websiteConfig = new WebsiteConfig($index,$error);

        $routingRule = new WebsiteRoutingRule();
        $routingRule->setNumber(1);
        $websiteCondition = new WebsiteCondition();
        $websiteCondition->setKeyPrefixEquals("abc/");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);
        $routingRule->setCondition($websiteCondition);
        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
        $websiteRedirect->setPassQueryString(true);
        $websiteRedirect->setMirrorURL('http://example.com/');
        $websiteRedirect->setMirrorPassQueryString(true);
        $websiteRedirect->setMirrorFollowRedirect(true);
        $websiteRedirect->setMirrorCheckMd5(false);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        // Whether to transparently transmit headers other than the following headers to the source station

        $mirrorHeaders->setPassAll(true);
        $pass = 'myheader-key1';
        $passOne = 'myheader-key2';
        $mirrorHeaders->addPass($pass);
        $mirrorHeaders->addPass($passOne);
        $remove = 'myheader-key3';
        $removeOne = 'myheader-key4';
        $mirrorHeaders->addRemove($remove);
        $mirrorHeaders->addRemove($removeOne);

        $set = new WebsiteMirrorHeadersSet();
        $set->setKey("myheader-key5");
        $set->setValue("myheader-value5");
        $mirrorHeaders->addSet($set);

        $websiteRedirect->setMirrorHeaders($mirrorHeaders);
        $routingRule->setRedirect($websiteRedirect);
        $websiteConfig->addRule($routingRule);

        $routingRule2 = new WebsiteRoutingRule();
        $routingRule2->setNumber(2);
        $websiteCondition2 = new WebsiteCondition();
        $websiteCondition2->setKeyPrefixEquals("abc/");
        $websiteCondition2->setHttpErrorCodeReturnedEquals(404);
        $includeHeader2 = new WebsiteIncludeHeader();
        $includeHeader2->setKey('host');
        $includeHeader2->setEquals('test.oss-cn-beijing-internal.aliyuncs.com');
        $websiteCondition2->addIncludeHeader($includeHeader2);
        $routingRule2->setCondition($websiteCondition2);

        $websiteRedirect2 = new WebsiteRedirect();
        $websiteRedirect2->setRedirectType(WebsiteRedirect::ALICDN);
        $websiteRedirect2->setPassQueryString(false);
        $websiteRedirect2->setProtocol("http");
        $websiteRedirect2->setHostName("example.com");
        $websiteRedirect2->setReplaceKeyWith("prefix/!{key}.suffix");
        $websiteRedirect2->setHttpRedirectCode(301);
        $routingRule2->setRedirect($websiteRedirect2);
        $websiteConfig->addRule($routingRule2);


        $this->assertTrue($this->cleanXml($this->manyXml) === $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testParseOneRule()
    {
        $index = new WebsiteIndexDocument('index.html',false,0);
        $error = new WebsiteErrorDocument('error.html',404);
        $websiteConfig = new WebsiteConfig($index,$error);

        $routingRule = new WebsiteRoutingRule();
        $routingRule->setNumber(1);
        $websiteCondition = new WebsiteCondition();
        $includeHeader = new WebsiteIncludeHeader();
        $includeHeader->setKey('host');
        $includeHeader->setEquals('test.oss-cn-beijing-internal.aliyuncs.com');
        $websiteCondition->addIncludeHeader($includeHeader);
        $websiteCondition->setKeyPrefixEquals("examplebucket");
        $websiteCondition->setHttpErrorCodeReturnedEquals(404);

        $websiteRedirect = new WebsiteRedirect();
        $websiteRedirect->setRedirectType(WebsiteRedirect::MIRROR);
        $websiteRedirect->setMirrorURL('https://www.example.com/');
        $websiteRedirect->setPassQueryString(true);
        $websiteRedirect->setMirrorPassQueryString(true);
        $websiteRedirect->setMirrorCheckMd5(true);

        $mirrorHeaders = new WebsiteMirrorHeaders();
        // Whether to transparently transmit headers other than the following headers to the source station

        $mirrorHeaders->setPassAll(true);
        $pass = 'cache-control-one';
        $passOne = 'pass-one';
        $mirrorHeaders->addPass($pass);
        $mirrorHeaders->addPass($passOne);
        $remove = 'remove-one';
        $removeOne = 'test-two';
        $mirrorHeaders->addRemove($remove);
        $mirrorHeaders->addRemove($removeOne);

        $set = new WebsiteMirrorHeadersSet();
        $set->setKey("key1");
        $set->setValue("value1");
        $mirrorHeaders->addSet($set);

        $websiteRedirect->setMirrorHeaders($mirrorHeaders);
        $websiteRedirect->setEnableReplacePrefix(false);
        $websiteRedirect->setReplaceKeyWith("key.jpg");
        $routingRule->setRedirect($websiteRedirect);
        $routingRule->setCondition($websiteCondition);
        $websiteConfig->addRule($routingRule);

        $this->assertTrue($this->cleanXml($this->oneRule) === $this->cleanXml($websiteConfig->serializeToXml()));
    }


    public function testWebsiteConstructNew()
    {
        $index = new WebsiteIndexDocument("index.html");
        $error = new WebsiteErrorDocument("errorDocument.html");
        $websiteConfig = new WebsiteConfig($index, $error);
        $this->assertEquals('index.html', $websiteConfig->getIndexDocument()->getSuffix());
        $this->assertEquals('errorDocument.html', $websiteConfig->getErrorDocument()->getKey());
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
