<?php

namespace ASN1;

use Proton\IosReceiptParser\ASN1\SimpleDecoder;
use phpseclib3\File\ASN1;
use PHPUnit\Framework\TestCase;

class SimpleDecoderTest extends TestCase
{
    private const DER_STR = 'DARhYmNk';
    private const SRC_STR = 'YWJjZA==';

    private const DER_INT = 'AgMB4kA=';
    private const SRC_INT = 123456;

    private const DER_ATTRS = 'MRswCwIBAQIBAQQDabcdMAwCAgKaAgEABAPDHLM=';
    private const SRC_ATTRS = [
        ['type' => 1, 'version' => 1, 'value' => 'abcd'],
        ['type' => 666, 'version' => 0, 'value' => 'wxyz'],
    ];

    /**
     * @dataProvider decoderProvider
     */
    public function testDecode(SimpleDecoder $decoder): void
    {
        $payload = $decoder->decode(base64_decode(self::DER_STR), ASN1::TYPE_UTF8_STRING);
        $this->assertSame(self::SRC_STR, base64_encode($payload));

        $payload = $decoder->decode(base64_decode(self::DER_INT), ['type' => ASN1::TYPE_INTEGER]);
        $this->assertSame((string) self::SRC_INT, (string) $payload);
    }

    /**
     * @dataProvider decoderProvider
     */
    public function testDecodeBase64(SimpleDecoder $decoder): void
    {
        $payload = $decoder->decodeBase64(self::DER_STR, ASN1::TYPE_UTF8_STRING);
        $this->assertSame(self::SRC_STR, base64_encode($payload));

        $payload = $decoder->decodeBase64(self::DER_INT, ['type' => ASN1::TYPE_INTEGER]);
        $this->assertSame((string) self::SRC_INT, (string) $payload);
    }

    /**
     * @dataProvider decoderProvider
     */
    public function testDecodeAttributesSet(SimpleDecoder $decoder): void
    {
        $payload = $decoder->decodeAttributesSet(base64_decode(self::DER_ATTRS));
        $this->assertIsArray($payload);
        $this->assertCount(count(self::SRC_ATTRS), $payload);
        $this->assertSame([0, 1], array_keys($payload), 'Payload was expected to be indexed array');

        foreach ($payload as $ix => $attr) {
            $this->assertSame((string) self::SRC_ATTRS[$ix]['type'], (string) $attr['type']);
            $this->assertSame((string) self::SRC_ATTRS[$ix]['version'], (string) $attr['version']);
            $this->assertSame(self::SRC_ATTRS[$ix]['value'], base64_encode($attr['value']));
        }
    }

    public function decoderProvider(): array
    {
        return [
            [new SimpleDecoder()],
        ];
    }
}
