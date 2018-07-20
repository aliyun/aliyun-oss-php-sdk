<?php

namespace OBS\Result;

use OBS\Model\GetLiveChannelInfo;

class GetLiveChannelInfoResult extends Result
{
    /**
     * @return
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channelList = new GetLiveChannelInfo();
        $channelList->parseFromXml($content);
        return $channelList;
    }
}
