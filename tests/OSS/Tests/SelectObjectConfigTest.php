<?php

namespace OSS\Tests;

use OSS\Model\LoggingConfig;
use OSS\Model\SelectObjectConfig;

class SelectObjectConfigTest extends \PHPUnit\Framework\TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<SelectRequest>
<Expression>
c2VsZWN0IGNvdW50KCopIGZyb20gb3Nzb2JqZWN0IHdoZXJlIF80ID4gNDU=
</Expression>
<InputSerialization>
<CompressionType>None|GZIP</CompressionType>
<JSON>
<Type>DOCUMENT|LINES</Type>
<Range>
line-range=start-end|split-range=start-end
</Range>
<ParseJsonNumberAsString> true|false
</ParseJsonNumberAsString>
</JSON>
</InputSerialization>
<OutputSerialization>
<JSON>
<RecordDelimiter>
Base64 of record delimiter
</RecordDelimiter>
</JSON>
<OutputRawData>false|true</OutputRawData>
<EnablePayloadCrc>true</EnablePayloadCrc>
</OutputSerialization>
<Options>
<SkipPartialDataRecord>
false|true
</SkipPartialDataRecord>
<MaxSkippedRecordsAllowed>
max allowed number of records skipped
</MaxSkippedRecordsAllowed>
</Options>
</SelectRequest>
BBBB;

    private $nullXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<SelectRequest>
</SelectRequest>
BBBB;

    public function testParseValidXml()
    {
        $config = new SelectObjectConfig();
        $config->parseFromXml($this->validXml);
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml(strval($config)));
    }


    public function testNullXml()
    {
        $config = new SelectObjectConfig();
        $config->parseFromXml($this->nullXml);
        $this->assertEquals($this->cleanXml($this->nullXml), $this->cleanXml(strval($config)));
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }
}
