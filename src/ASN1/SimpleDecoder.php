<?php

namespace Proton\IosReceiptParser\ASN1;

use phpseclib\File\ASN1;

/**
 * @internal
 * @psalm-import-type AttributeSequence from \Proton\IosReceiptParser\Attribute\AttributeSet
 */
final class SimpleDecoder
{
    private $asn1;

    public function __construct(ASN1 $asn1)
    {
        $this->asn1 = $asn1;
    }

    public function decodeBase64(string $base64, $type)
    {
        return $this->decode(base64_decode($base64), $type);
    }

    public function decode(string $binary, $type)
    {
        return $this->asn1->asn1map(
            $this->asn1->decodeBER($binary)[0],
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
