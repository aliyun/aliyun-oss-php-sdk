<?php

namespace OSS\Result;


/**
 * Class DeleteObjectsResult
 * @package OSS\Result
 */
class DeleteObjectsResult extends Result
{
    /**
     * @return array()
     */
    protected function parseDataFromResponse()
    {
        $body = $this->rawResponse->body;
        if (empty($body) || false === strpos($body, '<?xml')) {
            return '';
        }

        $xml = simplexml_load_string($body); 
        $objects = array();

        if (isset($xml->Deleted)) {
            foreach($xml->Deleted as $deleteKey)
                $objects[] = $deleteKey->Key;
        }
        return $objects;
    }
}
