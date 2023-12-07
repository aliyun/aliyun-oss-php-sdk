<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Model\ListLiveChannel;

class ListLiveChannelResult extends Result
{

    /**
     * @return ListLiveChannel
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new OssException("body is null");
        }
        $channelList = new ListLiveChannel();
        $channelList->parseFromXml($content);
        return $channelList;
    }
}
