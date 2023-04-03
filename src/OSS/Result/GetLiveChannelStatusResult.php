<?php

namespace OSS\Result;

use OSS\Model\GetLiveChannelStatus;


/**
 * Class GetLiveChannelStatusResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/44299.html
 */
class GetLiveChannelStatusResult extends Result
{
    /**
     * @return GetLiveChannelStatus
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channelList = new GetLiveChannelStatus();
        $channelList->parseFromXml($content);
        return $channelList;
    }
}
