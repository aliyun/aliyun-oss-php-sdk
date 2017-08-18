<?php

namespace OSS\Result;

use OSS\Core\OssException;
use OSS\Http\ResponseCore;


/**
 * Class Result, it's the base class for all operations' results.
 * The exact parsing logic is in its subclass.
 *
 * @package OSS\Model
 */
abstract class Result
{
    /**
     * Result constructor.
     * @param $response ResponseCore
     * @throws OssException
     */
    public function __construct($response)
    {
        if ($response === null) {
            throw new OssException("raw response is null");
        }
        $this->rawResponse = $response;
        $this->parseResponse();
    }

    /**
     * Gets requestId
     *
     * @return string
     */
    public function getRequestId()
    {
        if (isset($this->rawResponse) &&
            isset($this->rawResponse->header) &&
            isset($this->rawResponse->header['x-oss-request-id'])
        ) {
            return $this->rawResponse->header['x-oss-request-id'];
        } else {
            return '';
        }
    }

    /**
     * Gets the parsed data. The data's type varis for different subclass.
     *
     * $return mixed
     */
    public function getData()
    {
        return $this->parsedData;
    }

    /**
     * Parse the data from response. It's implemented in its subclass.
     *
     * @return mixed
     */
    abstract protected function parseDataFromResponse();

    /**
     * Gets the isOk flag.
     *
     * @return mixed
     */
    public function isOK()
    {
        return $this->isOk;
    }

    /**
     * @throws OssException
     */
    public function parseResponse()
    {
        $this->isOk = $this->isResponseOk();
        if ($this->isOk) {
            $this->parsedData = $this->parseDataFromResponse();
        } else {
            $httpStatus = strval($this->rawResponse->status);
            $requestId = strval($this->getRequestId());
            $code = $this->retrieveErrorCode($this->rawResponse->body);
            $message = $this->retrieveErrorMessage($this->rawResponse->body);
            $body = $this->rawResponse->body;

            $details = array(
                'status' => $httpStatus,
                'request-id' => $requestId,
                'code' => $code,
                'message' => $message,
                'body' => $body
            );
            throw new OssException($details);
        }
    }

    /**
     * Gets the error message from the response body. Returns empty if no error message.
     *
     * @param $body
     * @return string
     */
    private function retrieveErrorMessage($body)
    {
        if (empty($body) || false === strpos($body, '<?xml')) {
            return '';
        }
        $xml = simplexml_load_string($body);
        if (isset($xml->Message)) {
            return strval($xml->Message);
        }
        return '';
    }

    /**
     * Gets the error code from body. Returns empty if no error code.
     *
     * @param $body
     * @return string
     */
    private function retrieveErrorCode($body)
    {
        if (empty($body) || false === strpos($body, '<?xml')) {
            return '';
        }
        $xml = simplexml_load_string($body);
        if (isset($xml->Code)) {
            return strval($xml->Code);
        }
        return '';
    }

    /**
     * Checks the response status by its http status code. [200-299] means it's OK.
     *
     * @return bool
     */
    protected function isResponseOk()
    {
        $status = $this->rawResponse->status;
        if ((int)(intval($status) / 100) == 2) {
            return true;
        }
        return false;
    }

    /**
     * Returns the raw response of type ResponseCore 
     *
     * @return ResponseCore
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    /**
     * is response OK flag.
     */
    protected $isOk = false;
    /**
     * The parsed data which is implemented by its subclass.
     */
    protected $parsedData = null;
    /**
     * The raw response of type ResponseCore.
     *
     * @var ResponseCore
     */
    protected $rawResponse;
}