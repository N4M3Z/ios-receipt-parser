<?php

namespace Cthulhu\IosReceiptParser\ASN1;

use phpseclib3\File\ASN1;

/**
 * @internal
 * @psalm-import-type AttributeSequence from \Cthulhu\IosReceiptParser\Attribute\AttributeSet
 */
final class SimpleDecoder
{
    public function decodeBase64(string $base64, $type)
    {
        return $this->decode(base64_decode($base64), $type);
    }

    public function decode(string $binary, $type)
    {
        return ASN1::asn1map(
            ASN1::decodeBER($binary)[0],
            is_array($type) ? $type : ['type' => $type],
        );
    }

    /**
     * @psalm-return list<AttributeSequence>
     */
    public function decodeAttributesSet(string $binary): array
    {
        return $this->decode($binary, [
            'type' => ASN1::TYPE_SET,
            // Present of both 'min' and 'max' for ans1map means this is SetOf,
            // so 'children' can be specification for the child template instead of per-child definitions
            'min' => -1, 'max' => 1,
            'children' => [
                'type' => ASN1::TYPE_SEQUENCE,
                'children' => [
                    'type' => [
                        'type' => ASN1::TYPE_INTEGER,
                    ],
                    'version' => [
                        'type' => ASN1::TYPE_INTEGER,
                    ],
                    'value' => [
                        'type' => ASN1::TYPE_OCTET_STRING,
                    ],
                ],
            ],
        ]);
    }
}
