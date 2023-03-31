<?php

namespace OSS\Tests;

use OSS\Result\GetBucketCnameResult;
use OSS\Http\ResponseCore;

class GetBucketCnameResultTest extends \PHPUnit\Framework\TestCase
{

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<ListCnameResult>
<Bucket>targetbucket</Bucket>
<Owner>testowner</Owner>
<Cname>
<Domain>example.com</Domain>
<LastModified>2021-09-15T02:35:07.000Z</LastModified>
<Status>Enabled</Status>
<Certificate>
<Type>CAS</Type>
<CertId>493****-cn-hangzhou</CertId>
<Status>Enabled</Status>
<CreationDate>Wed, 15 Sep 2021 02:35:06 GMT</CreationDate>
<Fingerprint>DE:01:CF:EC:7C:A7:98:CB:D8:6E:FB:1D:97:EB:A9:64:1D:4E:**:**</Fingerprint>
<ValidStartDate>Tue, 12 Apr 2021 10:14:51 GMT</ValidStartDate>
<ValidEndDate>Mon, 4 May 2048 10:14:51 GMT</ValidEndDate>
</Certificate>
</Cname>
<Cname>
<Domain>example.org</Domain>
<LastModified>2021-09-15T02:34:58.000Z</LastModified>
<Status>Enabled</Status>
</Cname>
<Cname>
<Domain>example.edu</Domain>
<LastModified>2021-09-15T02:50:34.000Z</LastModified>
<Status>Enabled</Status>
</Cname>
</ListCnameResult>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetBucketCnameResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $config = $result->getData();
        $this->assertEquals("targetbucket", $config->getBucket());
        $this->assertEquals("testowner", $config->getOwner());

        $cnameList = $config->getCnameList();
        $cnameInfo = $cnameList[0];
        $this->assertEquals("example.com", $cnameInfo->getDomain());
        $this->assertEquals("Enabled", $cnameInfo->getStatus());
        $this->assertEquals("2021-09-15T02:35:07.000Z", $cnameInfo->getLastModified());
        $cert = $cnameInfo->getCertificate();
        $this->assertEquals("CAS", $cert->getType());
        $this->assertEquals("Enabled", $cert->getStatus());
        $this->assertEquals("493****-cn-hangzhou", $cert->getCertId());
        $this->assertEquals("Wed, 15 Sep 2021 02:35:06 GMT", $cert->getCreationDate());
        $this->assertEquals("DE:01:CF:EC:7C:A7:98:CB:D8:6E:FB:1D:97:EB:A9:64:1D:4E:**:**", $cert->getFingerprint());
        $this->assertEquals("Tue, 12 Apr 2021 10:14:51 GMT", $cert->getValidStartDate());
        $this->assertEquals("Mon, 4 May 2048 10:14:51 GMT", $cert->getValidEndDate());

        $cnameInfo1 = $cnameList[1];
        $this->assertEquals("example.org", $cnameInfo1->getDomain());
        $this->assertEquals("Enabled", $cnameInfo1->getStatus());
        $this->assertEquals("2021-09-15T02:34:58.000Z", $cnameInfo1->getLastModified());

        $cnameInfo2 = $cnameList[2];
        $this->assertEquals("example.edu", $cnameInfo2->getDomain());
        $this->assertEquals("Enabled", $cnameInfo2->getStatus());
        $this->assertEquals("2021-09-15T02:50:34.000Z", $cnameInfo2->getLastModified());

    }
}
