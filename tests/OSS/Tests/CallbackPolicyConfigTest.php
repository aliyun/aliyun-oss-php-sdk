<?php

namespace OSS\Tests;


use OSS\Model\CallbackPolicyConfig;
use OSS\Model\CallbackPolicyItem;

class CallbackPolicyConfigTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCallbackPolicy>
<PolicyItem>
<PolicyName>first</PolicyName>
<Callback>e1wiY2Fs...R7YnU=</Callback>
<CallbackVar>Q2FsbG...mJcIn0=</CallbackVar>
</PolicyItem>
<PolicyItem>
<PolicyName>second</PolicyName>
<Callback>e1wiY2Fsb...9keVwiOlwiYnVja2V0PSR7YnU=</Callback>
<CallbackVar>Q2Fs...FcIiwgXCJ4OmJcIjpcImJcIn0=</CallbackVar>
</PolicyItem>
</BucketCallbackPolicy>
BBBB;

    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCallbackPolicy>
<PolicyItem>
<PolicyName>first</PolicyName>
<Callback>e1wiY2Fs...R7YnU=</Callback>
<CallbackVar/>
</PolicyItem>
</BucketCallbackPolicy>
BBBB;

    public function testParseValidXml()
    {
        $websiteConfig = new CallbackPolicyConfig();
        $websiteConfig->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testParseValidXml1()
    {
        $websiteConfig = new CallbackPolicyConfig();
        $websiteConfig->parseFromXml($this->validXml1);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($websiteConfig->serializeToXml()));
    }

    public function testCallbackConfig()
    {

        $config = new CallbackPolicyConfig();
        $name = "first";
        $callback = 'e1wiY2Fs...R7YnU=';
        $callbackVar = 'Q2FsbG...mJcIn0=';

        $policyItem = new CallbackPolicyItem($name,$callback,$callbackVar);
        $config->addPolicyItem($policyItem);

        $name1 = "second";
        $callback1 = 'e1wiY2Fsb...9keVwiOlwiYnVja2V0PSR7YnU=';
        $callbackVar1 = 'Q2Fs...FcIiwgXCJ4OmJcIjpcImJcIn0=';

        $policyItem1 = new CallbackPolicyItem($name1,$callback1,$callbackVar1);
        $config->addPolicyItem($policyItem1);

        $this->assertEquals($this->cleanXml(strval($config)), $this->cleanXml($this->validXml));

        $config1 = new CallbackPolicyConfig();
        $name = "first";
        $callback = 'e1wiY2Fs...R7YnU=';

        $policyItem = new CallbackPolicyItem($name,$callback);
        $config1->addPolicyItem($policyItem);

        $this->assertEquals($this->cleanXml(strval($config1)), $this->cleanXml($this->validXml1));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
