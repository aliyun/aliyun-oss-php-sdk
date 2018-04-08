<?php

namespace OSS\Tests;

class StsBase
{
    public $SignatureVersion = "1.0";

    public $Version = "2015-04-01";

    public $Timestamp;

    public $SignatureMethod = "HMAC-SHA1";

    public $Format = "JSON";

    public $AccessKeyId;

    public $SignatureNonce;

    private $Signature;

}
