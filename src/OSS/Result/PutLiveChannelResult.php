<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\PutLiveChannel;

/**
 * Class PutLiveChannelResult
 * @package OSS\Result
 */
class PutLiveChannelResult extends Result
{

    /**
     * @return PutLiveChannel
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channel = new PutLiveChannel();
        $channel->parseFromXml($content);
        return $channel;
    }
}
