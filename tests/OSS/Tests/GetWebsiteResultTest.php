<?php

namespace OSS\Tests;


use OSS\Model\WebsiteRedirect;
use OSS\Model\WebsiteRoutingRules;
use OSS\Result\GetWebsiteResult;
use OSS\Http\ResponseCore;
use OSS\Core\OssException;
use OSS\Result\Result;

class GetWebsiteResultTest extends \PHPUnit\Framework\TestCase
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

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetWebsiteResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $websiteConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }


    private $oneXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
    <IndexDocument>
        <Suffix>index.html</Suffix>
        <Type>0</Type>
        <SupportSubDir>false</SupportSubDir>
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
            </Condition>
            <Redirect>
                <RedirectType>Mirror</RedirectType>
                <MirrorURL>https://www.example.com/</MirrorURL>
                <PassQueryString>true</PassQueryString>
                <MirrorPassQueryString>true</MirrorPassQueryString>
                <ReplaceKeyWith>key.jpg</ReplaceKeyWith>
                <EnableReplacePrefix>false</EnableReplacePrefix>
                <MirrorCheckMd5>true</MirrorCheckMd5>
                <MirrorHeaders>
                    <PassAll>true</PassAll>
                    <Pass>cache-control-one</Pass>
                    <Pass>pass-one</Pass>
                    <Remove>remove-one</Remove>
                    <Remove>test-two</Remove>
                    <Set>
                        <Key>key1</Key>
                        <Value>val1</Value>
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
                <Protocol>http</Protocol>
                <PassQueryString>false</PassQueryString>
                <ReplaceKeyWith>prefix/key.jpg</ReplaceKeyWith>
                <HttpRedirectCode>301</HttpRedirectCode>
                <MirrorHeaders>
                    <PassAll>true</PassAll>
                    <Pass>cache-control-one</Pass>
                    <Pass>pass-one</Pass>
                    <Remove>remove-one</Remove>
                    <Remove>test-two</Remove>
                    <Set>
                        <Key>key1</Key>
                        <Value>val1</Value>
                    </Set>
                    <Set>
                        <Key>key2</Key>
                        <Value>val2</Value>
                    </Set>
                </MirrorHeaders>
            </Redirect>
        </RoutingRule>
    </RoutingRules>
</WebsiteConfiguration>
BBBB;


    public function testValidWithMirrorOne(){
        $response = new ResponseCore(array(), $this->oneXml, 200);
        $result = new GetWebsiteResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $websiteConfig2 = $result->getData();

        $this->assertEquals("index.html", $websiteConfig2->getIndexDocument()->getSuffix());
        $this->assertEquals("error.html", $websiteConfig2->getErrorDocument()->getKey());
        $this->assertEquals(false,$websiteConfig2->getIndexDocument()->getSupportSubDir());
        $this->assertEquals(0, $websiteConfig2->getIndexDocument()->getType());
        $this->assertEquals(404, $websiteConfig2->getErrorDocument()->getHttpStatus());

        $rule = $websiteConfig2->getRoutingRules();

        // 1.test Condition
        $this->assertEquals("examplebucket", $rule[0]->getCondition()->getKeyPrefixEquals());
        $this->assertEquals(404, $rule[0]->getCondition()->getHttpErrorCodeReturnedEquals());

        $includeObject = $rule[1]->getCondition()->getIncludeHeader();
        $this->assertEquals('host',$includeObject[0]->getKey());
        $this->assertEquals('test.oss-cn-beijing-internal.aliyuncs.com',$includeObject[0]->getEquals());

        // 2.test Redirect
        $this->assertEquals("Mirror", $rule[0]->getRedirect()->getRedirectType());
        $this->assertEquals(true,$rule[0]->getRedirect()->getMirrorPassQueryString());
        $this->assertEquals("https://www.example.com/", $rule[0]->getRedirect()->getMirrorURL());
        $this->assertEquals(true,$rule[0]->getRedirect()->getMirrorPassQueryString());
        $this->assertEquals('key.jpg', $rule[0]->getRedirect()->getReplaceKeyWith());
        $this->assertEquals(false,$rule[0]->getRedirect()->getEnableReplacePrefix());
        $this->assertEquals(true,$rule[0]->getRedirect()->getMirrorCheckMd5());


        // 2.1 test Redirect mirror header
        $headerObject = $rule[0]->getRedirect()->getMirrorHeaders();
        $this->assertEquals('pass-one',$headerObject->getPass()[1]);
        $this->assertEquals('remove-one',$headerObject->getRemove()[0]);
        $this->assertEquals('key1',$headerObject->getSet()[0]->getKey());
        $this->assertEquals('val1',$headerObject->getSet()[0]->getValue());

        $headerObject2 = $rule[1]->getRedirect()->getMirrorHeaders();

        $this->assertEquals('key2',$headerObject2->getSet()[1]->getKey());
        $this->assertEquals('val2',$headerObject2->getSet()[1]->getValue());

    }

private $twoXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
<IndexDocument>
<Suffix>index.html</Suffix>
</IndexDocument>
<ErrorDocument>
<Key>error.html</Key>
</ErrorDocument>
<RoutingRules>
<RoutingRule>
<RuleNumber>1</RuleNumber>
<Condition>
<KeyPrefixEquals>abc/</KeyPrefixEquals>
<HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
<IncludeHeader>
<Key>host</Key>
<Equals>test.oss-cn-beijing-internal.aliyuncs.com</Equals>
</IncludeHeader>
<IncludeHeader>
<Key>host_two</Key>
<Equals>demo.oss-cn-beijing-internal.aliyuncs.com</Equals>
</IncludeHeader>
</Condition>
<Redirect>
<RedirectType>AliCDN</RedirectType>
<Protocol>http</Protocol>
<PassQueryString>false</PassQueryString>
<ReplaceKeyWith>prefix/key.jpg</ReplaceKeyWith>
<HttpRedirectCode>301</HttpRedirectCode>
</Redirect>
</RoutingRule>
</RoutingRules>
</WebsiteConfiguration>
BBBB;

    public function testValidWithMirrorTwo(){
        $response = new ResponseCore(array(), $this->twoXml, 200);
        $result = new GetWebsiteResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $websiteConfig2 = $result->getData();
        $this->assertEquals("index.html", $websiteConfig2->getIndexDocument()->getSuffix());
        $this->assertEquals("error.html", $websiteConfig2->getErrorDocument()->getKey());

        $rule = $websiteConfig2->getRoutingRules();
        // 1.test Condition
        $this->assertEquals("abc/", $rule[0]->getCondition()->getKeyPrefixEquals());
        $this->assertEquals(404, $rule[0]->getCondition()->getHttpErrorCodeReturnedEquals());
        // 1.1 test Condition Inclue Header
        $headerObject = $rule[0]->getCondition()->getIncludeHeader();
        $this->assertEquals('host',$headerObject[0]->getKey());
        $this->assertEquals('demo.oss-cn-beijing-internal.aliyuncs.com',$headerObject[1]->getEquals());

        // 2.test Redirect
        $this->assertEquals(WebsiteRedirect::ALICDN, $rule[0]->getRedirect()->getRedirectType());
        $this->assertEquals(WebsiteRedirect::HTTP, $rule[0]->getRedirect()->getProtocol());
        $this->assertEquals(false,$rule[0]->getRedirect()->getPassQueryString());
        $this->assertEquals(301, $rule[0]->getRedirect()->getHttpRedirectCode());
        $this->assertEquals('prefix/key.jpg', $rule[0]->getRedirect()->getReplaceKeyWith());
    }


    private $xml3 = <<<bbbbb
<?xml version="1.0" encoding="utf-8"?>
<WebsiteConfiguration>
    <IndexDocument>
        <Suffix>index.html</Suffix>
    </IndexDocument>
    <ErrorDocument>
        <Key>error.html</Key>
    </ErrorDocument>
    <RoutingRules>
        <RoutingRule>
            <RuleNumber>1</RuleNumber>
            <Condition>
                <HttpErrorCodeReturnedEquals>404</HttpErrorCodeReturnedEquals>
            </Condition>
            <Redirect>
                <ReplaceKeyWith>prefix/key</ReplaceKeyWith>
                <HttpRedirectCode>302</HttpRedirectCode>
                <EnableReplacePrefix>false</EnableReplacePrefix>
                <PassQueryString>false</PassQueryString>
                <RedirectType>External</RedirectType>
                <Protocol>https</Protocol>
                <HostName>demo.com</HostName>
            </Redirect>
        </RoutingRule>
    </RoutingRules>
</WebsiteConfiguration>
bbbbb;

    public function testXmlWithMirrorThree(){
        $response = new ResponseCore(array(), $this->xml3, 200);
        $result = new GetWebsiteResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $websiteConfig2 = $result->getData();
        $this->assertEquals("index.html", $websiteConfig2->getIndexDocument()->getSuffix());
        $this->assertEquals("error.html", $websiteConfig2->getErrorDocument()->getKey());
        $rule = $websiteConfig2->getRoutingRules();
        // 1.test Condition
        $this->assertEquals(404, $rule[0]->getCondition()->getHttpErrorCodeReturnedEquals());
        // 2.test Redirect
        $this->assertEquals(WebsiteRedirect::EXTERNAL, $rule[0]->getRedirect()->getRedirectType());
        $this->assertEquals(WebsiteRedirect::HTTPS, $rule[0]->getRedirect()->getProtocol());
        $this->assertEquals("demo.com", $rule[0]->getRedirect()->getHostName());
        $this->assertEquals(false,$rule[0]->getRedirect()->getPassQueryString());
        $this->assertEquals(false,$rule[0]->getRedirect()->getEnableReplacePrefix());
        $this->assertEquals(302, $rule[0]->getRedirect()->getHttpRedirectCode());
        $this->assertEquals('prefix/key', $rule[0]->getRedirect()->getReplaceKeyWith());

    }

    public function testInvalidResponse()
    {
        $response = new ResponseCore(array(), $this->validXml, 300);
        try {
            $result = new GetWebsiteResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }
}
