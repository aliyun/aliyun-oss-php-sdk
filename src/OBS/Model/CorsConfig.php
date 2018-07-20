<?php

namespace OBS\Model;


use OBS\Core\ObsException;

/**
 * Class CorsConfig
 * @package OBS\Model
 *
 * @link http://help.aliyun.com/document_detail/obs/api-reference/cors/PutBucketcors.html
 */
class CorsConfig implements XmlConfig
{
    /**
     * CorsConfig constructor.
     */
    public function __construct()
    {
        $this->rules = array();
    }

    /**
     * Get CorsRule list
     *
     * @return CorsRule[]
     */
    public function getRules()
    {
        return $this->rules;
    }


    /**
     * Add a new CorsRule
     *
     * @param CorsRule $rule
     * @throws ObsException
     */
    public function addRule($rule)
    {
        if (count($this->rules) >= self::OBS_MAX_RULES) {
            throw new ObsException("num of rules in the config exceeds self::OBS_MAX_RULES: " . strval(self::OBS_MAX_RULES));
        }
        $this->rules[] = $rule;
    }

    /**
     * Parse CorsConfig from the xml.
     *
     * @param string $strXml
     * @throws ObsException
     * @return null
     */
    public function parseFromXml($strXml)
    {
        $xml = simplexml_load_string($strXml);
        if (!isset($xml->CORSRule)) return;
        foreach ($xml->CORSRule as $rule) {
            $corsRule = new CorsRule();
            foreach ($rule as $key => $value) {
                if ($key === self::OBS_CORS_ALLOWED_HEADER) {
                    $corsRule->addAllowedHeader(strval($value));
                } elseif ($key === self::OBS_CORS_ALLOWED_METHOD) {
                    $corsRule->addAllowedMethod(strval($value));
                } elseif ($key === self::OBS_CORS_ALLOWED_ORIGIN) {
                    $corsRule->addAllowedOrigin(strval($value));
                } elseif ($key === self::OBS_CORS_EXPOSE_HEADER) {
                    $corsRule->addExposeHeader(strval($value));
                } elseif ($key === self::OBS_CORS_MAX_AGE_SECONDS) {
                    $corsRule->setMaxAgeSeconds(strval($value));
                }
            }
            $this->addRule($corsRule);
        }
        return;
    }

    /**
     * Serialize the object into xml string.
     *
     * @return string
     */
    public function serializeToXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><CORSConfiguration></CORSConfiguration>');
        foreach ($this->rules as $rule) {
            $xmlRule = $xml->addChild('CORSRule');
            $rule->appendToXml($xmlRule);
        }
        return $xml->asXML();
    }

    public function __toString()
    {
        return $this->serializeToXml();
    }

    const OBS_CORS_ALLOWED_ORIGIN = 'AllowedOrigin';
    const OBS_CORS_ALLOWED_METHOD = 'AllowedMethod';
    const OBS_CORS_ALLOWED_HEADER = 'AllowedHeader';
    const OBS_CORS_EXPOSE_HEADER = 'ExposeHeader';
    const OBS_CORS_MAX_AGE_SECONDS = 'MaxAgeSeconds';
    const OBS_MAX_RULES = 10;

    /**
     * CorsRule list
     *
     * @var CorsRule[]
     */
    private $rules = array();
}