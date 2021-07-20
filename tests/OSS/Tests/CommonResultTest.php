<?php

namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Result\CommonResult;


class CommonResultTest extends \PHPUnit\Framework\TestCase
{
    public function testParseValid200()
    {
        $response = new ResponseCore(array('header'), array('body'), 200);
        $result = new CommonResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), array('header','body'));
    }

    public function testParseInvalid404()
    {
        $response = new ResponseCore(array(), array(), 200);
        $result = new CommonResult($response);
        $this->assertTrue($result->isOK());
        $this->assertEquals($result->getData(), array());
    }
}
