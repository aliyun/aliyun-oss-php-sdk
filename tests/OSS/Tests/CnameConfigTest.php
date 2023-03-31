<?php

namespace OSS\Tests;


use OSS\Model\CnameConfig;
use OSS\Model\CnameConfigCertificate;
use OSS\Model\CnameInfo;


class CnameConfigTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCnameConfiguration><Cname><Domain>example.com</Domain></Cname></BucketCnameConfiguration>
BBBB;


    private $validXml1 = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketCnameConfiguration>
<Cname>
<Domain>example.com</Domain>
<CertificateConfiguration>
<CertId>493****-cn-hangzhou</CertId>
<Certificate>-----BEGIN CERTIFICATE----- MIIDhDCCAmwCCQCFs8ixARsyrDANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMC **** -----END CERTIFICATE-----</Certificate>
<PrivateKey>-----BEGIN CERTIFICATE----- MIIDhDCCAmwCCQCFs8ixARsyrDANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMC **** -----END CERTIFICATE-----</PrivateKey>
<PreviousCertId>493****-cn-hangzhou</PreviousCertId>
<Force>true</Force>
<DeleteCertificate>false</DeleteCertificate>
</CertificateConfiguration>
</Cname>
</BucketCnameConfiguration>
BBBB;

    public function testValidXml()
    {
        $config = new CnameConfig();
        $config->setCname("example.com");

        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($config->serializeToXml()));
    }

    public function testValidXml1()
    {
        $config = new CnameConfig();
        $certificate = new CnameConfigCertificate();
        $certificate->setCertId("493****-cn-hangzhou");
        $certificate->setCertificate("-----BEGIN CERTIFICATE----- MIIDhDCCAmwCCQCFs8ixARsyrDANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMC **** -----END CERTIFICATE-----");
        $certificate->setPrivateKey("-----BEGIN CERTIFICATE----- MIIDhDCCAmwCCQCFs8ixARsyrDANBgkqhkiG9w0BAQsFADCBgzELMAkGA1UEBhMC **** -----END CERTIFICATE-----");
        $certificate->setPreviousCertId("493****-cn-hangzhou");
        $certificate->setForce(true);
        $certificate->setDeleteCertificate(false);

        $config->setCname("example.com");
        $config->setCertificateConfig($certificate);

        $this->assertEquals($this->cleanXml($this->validXml1), $this->cleanXml($config->serializeToXml()));
    }


    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

}
