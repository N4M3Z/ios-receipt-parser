<?php

namespace Proton\IosReceiptParser;

use Proton\IosReceiptParser\ASN1\Pkcs7Reader;
use Proton\IosReceiptParser\ASN1\Pkcs7UnverifiedParser;
use Proton\IosReceiptParser\ASN1\SimpleDecoder;
use DateTime;

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

    /**
     * @throws \Exception
     */
    public function parseUsingOnlyTrustedCerts(string $receipt, string ...$certificate): Receipt
    {
        $payload = $this->pkcs7Reader->readUsingOnlyTrustedCerts(base64_decode($receipt), ...$certificate);

        return $this->parsePayload($payload);
    }

    private function parsePayload(string $payload): Receipt
    {
        return new Receipt($this->decoder->decodeAttributesSet($payload), $this->decoder);
    }

    public static function convertTimestampMs(?string $time): ?string
    {
        if ($time === null) {
            return null;
        }
        $datetime = DateTime::createFromFormat(DateTime::ATOM, $time);
        return (string) $datetime->getTimestamp() . $datetime->format('v');
    }
}
