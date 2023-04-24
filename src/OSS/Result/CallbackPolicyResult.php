<?php


namespace OSS\Result;

use OSS\Model\CallbackPolicyConfig;

/**
 * Class CallbackResult
 * @package OSS\Result
 */
class CallbackPolicyResult extends Result
{

    /**
     * @return CallbackPolicyConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new CallbackPolicyConfig();
        $config->parseFromXml($content);
        return $config;
    }
}
