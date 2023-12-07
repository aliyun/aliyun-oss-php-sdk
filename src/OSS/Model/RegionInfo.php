<?php
namespace OSS\Model;

/**
 * Class RegionInfo
 * @package OSS\Model
 * @link https://help.aliyun.com/document_detail/345596.html
 */
class RegionInfo {

    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $internetEndpoint;
    /**
     * @var string
     */
    private $internalEndpoint;
    /**
     * @var string
     */
    private $accelerateEndpoint;


    /**
     * RegionInfo constructor.
     * @param string $region
     * @param string $internetEndpoint
     * @param string $internalEndpoint
     * @param string $accelerateEndpoint
     */
    public function __construct($region, $internetEndpoint, $internalEndpoint, $accelerateEndpoint) {
        $this->region = $region;
        $this->internetEndpoint = $internetEndpoint;
        $this->internalEndpoint = $internalEndpoint;
        $this->accelerateEndpoint = $accelerateEndpoint;
    }


    /**
     * @return string
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getInternetEndpoint() {
        return $this->internetEndpoint;
    }

    /**
     * @return string
     */
    public function getInternalEndpoint() {
        return $this->internalEndpoint;
    }

    /**
     * @return string
     */
    public function getAccelerateEndpoint() {
        return $this->accelerateEndpoint;
    }
}

