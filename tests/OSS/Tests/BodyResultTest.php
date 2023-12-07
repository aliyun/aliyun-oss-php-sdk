<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\BodyResult;


class BodyResultTest extends \PHPUnit\Framework\TestCase
{
    public function testParseValid200()
    {
        $response = new ResponseCore(array(), "hi", 200);
        $result = new BodyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), "hi");

        $response = new ResponseCore(array(), false, 200);
        $result = new BodyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), false);

        $response = new ResponseCore(array(), 0, 200);
        $result = new BodyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), 0);

        $response = new ResponseCore(array(), "false", 200);
        $result = new BodyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), "false");
    }

    public function testParseInvalid404()
    {
        $response = new ResponseCore(array(), null, 200);
        $result = new BodyResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), "");
    }
}
