<?php
/**
 * Created by PhpStorm.
 * User: yangpeng
 * Date: 2021/7/13
 * Time: 10:37
 */

namespace OSS\Result;


/**
 * Class CommonResult
 * @package OSS\Result
 */
class CommonResult extends Result
{

    /**
     * @return array
     */
    protected function parseDataFromResponse()
    {
        $body = array('body' => $this->rawResponse->body);
        return array_merge($this->rawResponse->header, $body);
    }
}
