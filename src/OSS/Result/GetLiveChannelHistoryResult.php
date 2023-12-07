<?php

namespace OSS\Result;

use OSS\Model\GetLiveChannelHistory;


/**
 * Class GetLiveChannelHistoryResult
 * @package OSS\Result
 * @link https://help.aliyun.com/document_detail/44301.html
 */
class GetLiveChannelHistoryResult extends Result
{
    /**
     * @return GetLiveChannelHistory
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $channelList = new GetLiveChannelHistory();
        $channelList->parseFromXml($content);
        return $channelList;
    }
}
