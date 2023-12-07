<?php

namespace OSS\Tests;

use OSS\Core\OssException;
use OSS\Http\ResponseCore;
use OSS\Result\BodyResult;
use OSS\Result\CallbackPolicyResult;
use OSS\Result\Result;


class CallbackPolicyResultTest extends \PHPUnit\Framework\TestCase
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
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new CallbackPolicyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($config->serializeToXml()));
        $item = $config->getPolicyItem();
        $this->assertEquals("first", $item[0]->getPolicyName());
        $this->assertEquals("e1wiY2Fs...R7YnU=",  $item[0]->getCallback());
        $this->assertEquals("Q2FsbG...mJcIn0=",  $item[0]->getCallbackVar());

        $this->assertEquals("second", $item[1]->getPolicyName());
        $this->assertEquals("e1wiY2Fsb...9keVwiOlwiYnVja2V0PSR7YnU=",  $item[1]->getCallback());
        $this->assertEquals("Q2Fs...FcIiwgXCJ4OmJcIjpcImJcIn0=",  $item[1]->getCallbackVar());
    }

    public function testParseValidXml1()
    {
        $response = new ResponseCore(array(), $this->validXml1, 200);
        $result = new CallbackPolicyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($config->serializeToXml()));
        $item = $config->getPolicyItem();
        $this->assertEquals("first", $item[0]->getPolicyName());
        $this->assertEquals("e1wiY2Fs...R7YnU=",  $item[0]->getCallback());
        $this->assertEquals("",  $item[0]->getCallbackVar());

    }


    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

    public function testInvalidResponse()
    {
        $response = new ResponseCore(array(), $this->validXml, 404);
        try {
            $result = new CallbackPolicyResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }
}
