<?php

namespace OBS\Result;

use OBS\Core\ObsUtil;
use OBS\Model\ListMultipartUploadInfo;
use OBS\Model\UploadInfo;


/**
 * Class ListMultipartUploadResult
 * @package OBS\Result
 */
class ListMultipartUploadResult extends Result
{
    /**
     * Parse the return data from the ListMultipartUpload interface
     *
     * @return ListMultipartUploadInfo
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $xml = simplexml_load_string($content);

        $encodingType = isset($xml->EncodingType) ? strval($xml->EncodingType) : "";
        $bucket = isset($xml->Bucket) ? strval($xml->Bucket) : "";
        $keyMarker = isset($xml->KeyMarker) ? strval($xml->KeyMarker) : "";
        $keyMarker = ObsUtil::decodeKey($keyMarker, $encodingType);
        $uploadIdMarker = isset($xml->UploadIdMarker) ? strval($xml->UploadIdMarker) : "";
        $nextKeyMarker = isset($xml->NextKeyMarker) ? strval($xml->NextKeyMarker) : "";
        $nextKeyMarker = ObsUtil::decodeKey($nextKeyMarker, $encodingType);
        $nextUploadIdMarker = isset($xml->NextUploadIdMarker) ? strval($xml->NextUploadIdMarker) : "";
        $delimiter = isset($xml->Delimiter) ? strval($xml->Delimiter) : "";
        $delimiter = ObsUtil::decodeKey($delimiter, $encodingType);
        $prefix = isset($xml->Prefix) ? strval($xml->Prefix) : "";
        $prefix = ObsUtil::decodeKey($prefix, $encodingType);
        $maxUploads = isset($xml->MaxUploads) ? intval($xml->MaxUploads) : 0;
        $isTruncated = isset($xml->IsTruncated) ? strval($xml->IsTruncated) : "";
        $listUpload = array();

        if (isset($xml->Upload)) {
            foreach ($xml->Upload as $upload) {
                $key = isset($upload->Key) ? strval($upload->Key) : "";
                $key = ObsUtil::decodeKey($key, $encodingType);
                $uploadId = isset($upload->UploadId) ? strval($upload->UploadId) : "";
                $initiated = isset($upload->Initiated) ? strval($upload->Initiated) : "";
                $listUpload[] = new UploadInfo($key, $uploadId, $initiated);
            }
        }
        return new ListMultipartUploadInfo($bucket, $keyMarker, $uploadIdMarker,
            $nextKeyMarker, $nextUploadIdMarker,
            $delimiter, $prefix, $maxUploads, $isTruncated, $listUpload);
    }
}