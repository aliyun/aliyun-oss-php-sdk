<?php
namespace OSS\Tests;

use OSS\Http\ResponseCore;
use OSS\Model\StorageCapacityConfig;
use OSS\Result\GetStorageCapacityResult;
use OSS\Core\OssException;

class StorageCapacityTest extends \PHPUnit\Framework\TestCase
{

    private $inValidXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<BucketUserQos>
   <OssStorageCapacity>1</OssStorageCapacity>
</BucketUserQos>
BBBB;

    private $validXml = <<<BBBB
<?xml version="1.0" encoding="UTF-8"?>
<BucketUserQos>
   <StorageCapacity>1</StorageCapacity>
</BucketUserQos>
BBBB;

    public function testParseInValidXml()
    {
        $response = new ResponseCore(array(), $this->inValidXml, 300);
        try {
            new GetStorageCapacityResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }

    public function testParseEmptyXml()
    {
        $response = new ResponseCore(array(), "", 300);
        try {
            new GetStorageCapacityResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {
            $this->assertTrue(true);
        }
    }

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetStorageCapacityResult($response);
        $this->assertEquals($result->getData(), 1);
    }

    public function testSerializeToXml()
    {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<BucketUserQos><StorageCapacity>1</StorageCapacity></BucketUserQos>\n";
        
        $storageCapacityConfig = new StorageCapacityConfig(1);
        $content = $storageCapacityConfig->serializeToXml();
        $this->assertEquals($content, $xml);
    }
}


https://example-walker.oss-cn-chengdu.aliyuncs.com/user-dir-prefix/ljjemllljcmogpfapbkkighbhhppjdbg.zip?Expires=1625740937&OSSAccessKeyId=TMP.3KgLbqTFtGEDNmHPiWHp3PnuC1sgXUMyk1rcLfySaA8nb26k2USJtH3F23wMhbNCDcVzCiNFW77zzcqw1Zs9Lo5svv2fFP&Signature=NUQhonUH0mTGuf76vN16VBEn40I%3D&versionId=CAEQURiBgICW0f2V1BciIDE2MmI3MWRiYzkxNTQyN2M5MjVjYjI2MmRiOWQ3MGZk&response-content-type=application%2Foctet-stream