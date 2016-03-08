<?php

namespace OSS\Result;

use OSS\Model\LiveChannelInfo;

class PutLiveChannelResult extends Result
{
    /**
     * @return
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channel = new LiveChannelInfo();
        $channel->parseFromXml($content);
        return $channel;
    }
}