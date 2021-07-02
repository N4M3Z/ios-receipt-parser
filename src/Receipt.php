<?php

namespace Cthulhu\IosReceiptParser;

use Cthulhu\IosReceiptParser\ASN1\SimpleDecoder;
use Cthulhu\IosReceiptParser\Attribute\AttributeSet;
use Cthulhu\IosReceiptParser\Attribute\AttributeType;
use phpseclib3\File\ASN1;

/**
 * @psalm-import-type AttributeSequence from AttributeSet
 */
final class Receipt implements \JsonSerializable
{
    /** @var AttributeSet */
    private $attributes;

    /** @var SimpleDecoder */
    private $decoder;

    /**
     * @psalm-param list<AttributeSet> $data
     */
    public function __construct(array $data, SimpleDecoder $decoder)
    {
        $this->attributes = new AttributeSet($data, 'receipt');
        $this->decoder = $decoder;
    }

    public function getBundleId(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::RECEIPT_BUNDLE_ID),
            ASN1::TYPE_UTF8_STRING,
        );
    }

    public function getAppVersion(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::RECEIPT_APP_VERSION),
            ASN1::TYPE_UTF8_STRING,
        );
    }

    public function getOpaque(): string
    {
        return base64_decode($this->attributes->getRequired(AttributeType::RECEIPT_OPAQUE));
    }

    public function getSha1(): string
    {
        return base64_decode($this->attributes->getRequired(AttributeType::RECEIPT_SHA1));
    }

    public function getCreationDate(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::RECEIPT_CREATION_DATE),
            ASN1::TYPE_IA5_STRING,
        );
    }

    public function getInApp(): array
    {
        return array_map(function (string $inApp): InApp {
            $attributes = $this->decoder->decodeAttributesSet(base64_decode($inApp));
            return new InApp($attributes, $this->decoder);
        }, $this->attributes->getMulti(AttributeType::RECEIPT_IN_APP));
    }

    public function getOriginalAppVersion(): string
    {
        $raw = $this->attributes->get(AttributeType::RECEIPT_ORIGINAL_APP_VERSION);

        return $raw === null
            ? $this->getAppVersion()
            : $this->decoder->decodeBase64($raw, ASN1::TYPE_UTF8_STRING);
    }

    public function getExpirationDate(): string
    {
        return $this->decoder->decodeBase64(
            $this->attributes->getRequired(AttributeType::RECEIPT_EXPIRATION_DATE),
            ASN1::TYPE_IA5_STRING,
        );
    }

    public function jsonSerialize(): array
    {
        $return = [];

        foreach ([
            AttributeType::RECEIPT_BUNDLE_ID => $this->getBundleId(),
            AttributeType::RECEIPT_APP_VERSION => $this->getAppVersion(),
            AttributeType::RECEIPT_CREATION_DATE => $this->getCreationDate(),
            AttributeType::RECEIPT_IN_APP => array_map(function (InApp $inApp): array {
                return $inApp->jsonSerialize();
            }, $this->getInApp()),
            AttributeType::RECEIPT_ORIGINAL_APP_VERSION => $this->getOriginalAppVersion(),
            AttributeType::RECEIPT_EXPIRATION_DATE => $this->getExpirationDate(),
        ] as $type => $value) {
            $return[AttributeType::getJsonFieldName($type)] = $value;
        }

        return $return;
    }
}
