<?php

namespace Cthulhu\IosReceiptParser;

use Cthulhu\IosReceiptParser\ASN1\Pkcs7Reader;
use Cthulhu\IosReceiptParser\ASN1\Pkcs7UnverifiedParser;
use Cthulhu\IosReceiptParser\ASN1\SimpleDecoder;

final class Parser
{
    /** @var Pkcs7Reader */
    private $pkcs7Reader;

    /** @var SimpleDecoder */
    private $decoder;

    public function __construct(Pkcs7Reader $pkcs7Reader = null)
    {
        $this->pkcs7Reader = $pkcs7Reader ?? new Pkcs7UnverifiedParser();
        $this->decoder = new SimpleDecoder();
    }

    public function parseUnverified(string $receipt): Receipt
    {
        $payload = $this->pkcs7Reader->readUnverified(base64_decode($receipt));

        return $this->parsePayload($payload);
    }

    public function parseUsingOnlyTrustedCerts(string $receipt, string ...$certificate): Receipt
    {
        $payload = $this->pkcs7Reader->readUsingOnlyTrustedCerts(base64_decode($receipt), ...$certificate);

        return $this->parsePayload($payload);
    }

    private function parsePayload(string $payload): Receipt
    {
        return new Receipt($this->decoder->decodeAttributesSet($payload), $this->decoder);
    }
}
