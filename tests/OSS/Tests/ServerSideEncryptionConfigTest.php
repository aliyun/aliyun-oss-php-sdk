<?php

namespace OSS\Tests;

use OSS\Model\ServerSideEncryptionConfig;

class ServerSideEncryptionConfigTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ServerSideEncryptionRule>
<ApplyServerSideEncryptionByDefault>
<SSEAlgorithm>KMS</SSEAlgorithm>
<KMSMasterKeyID>kms-id</KMSMasterKeyID>
<KMSDataEncryption>SM4</KMSDataEncryption>
</ApplyServerSideEncryptionByDefault>
</ServerSideEncryptionRule>
BBBB;
    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ServerSideEncryptionRule>
<ApplyServerSideEncryptionByDefault>
<SSEAlgorithm>SM4</SSEAlgorithm>
<KMSMasterKeyID/>
</ApplyServerSideEncryptionByDefault>
</ServerSideEncryptionRule>
BBBB;

    private $validXml2 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<ServerSideEncryptionRule>
<ApplyServerSideEncryptionByDefault>
<SSEAlgorithm>AES256</SSEAlgorithm>
<KMSMasterKeyID/>
</ApplyServerSideEncryptionByDefault>
</ServerSideEncryptionRule>
BBBB;

    public function testParseValidXml()
    {
        $config = new ServerSideEncryptionConfig();
        $config->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($config)));
        $this->assertEquals("kms-id",$config->getKMSMasterKeyID());
        $this->assertEquals("KMS",$config->getSSEAlgorithm());
        $this->assertEquals("SM4",$config->getKMSDataEncryption());
    }

    public function testValidXml1()
    {
        $config = new ServerSideEncryptionConfig();
        $config->parseFromXml($this->validXml1);
        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml(strval($config)));
        $this->assertEquals("",$config->getKMSMasterKeyID());
        $this->assertEquals("SM4",$config->getSSEAlgorithm());
        $this->assertEquals(null,$config->getKMSDataEncryption());
    }

    public function testValidXml2()
    {
        $config = new ServerSideEncryptionConfig();
        $config->parseFromXml($this->validXml2);
        $this->assertEquals($this->cleanXml($this->validXml2), $this->cleanXml(strval($config)));
        $this->assertEquals("",$config->getKMSMasterKeyID());
        $this->assertEquals("AES256",$config->getSSEAlgorithm());
    }


    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
