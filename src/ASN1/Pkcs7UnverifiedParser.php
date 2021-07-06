<?php

namespace Cthulhu\IosReceiptParser\ASN1;

use phpseclib3\File\ASN1;

class Pkcs7UnverifiedParser implements Pkcs7Reader
{
    private $decoder;

    public function __construct()
    {
        $this->decoder = new SimpleDecoder();
    }

    public function readUnverified(string $ber): string
    {
        $data = $this->decoder->decode($ber, [
            'type' => ASN1::TYPE_SEQUENCE,
            'children' => [
                '_id' => ['type' => ASN1::TYPE_ANY],
                'data' => [
                    'type' => ASN1::TYPE_SEQUENCE,
                    'constant' => 0,
                    'explicit' => true,
                    'children' => [
                        '_ver' => ['type' => ASN1::TYPE_ANY],
                        '_sigAlg' => ['type' => ASN1::TYPE_ANY],
                        'data' => [
                            'type' => ASN1::TYPE_SEQUENCE,
                            'children' => [
                                '_id' => ['type' => ASN1::TYPE_ANY],
                                'data' => [
                                    'type' => ASN1::TYPE_OCTET_STRING,
                                    'constant' => 0,
                                    'explicit' => true,
                                ],
                            ],
                        ],
                        '_certs' => [
                            'type' => ASN1::TYPE_ANY,
                            'constant' => 0,
                            'implicit' => true,
                            'optional' => true,
                        ],
                        '_crls' => [
                            'type' => ASN1::TYPE_ANY,
                            'constant' => 0,
                            'implicit' => true,
                            'optional' => true,
                        ],
                        '_sig' => ['type' => ASN1::TYPE_ANY],
                    ],
                ],
            ],
        ]);

        return $data['data']['data']['data'];
    }

    public function readUsingOnlyTrustedCerts(string $ber, string ...$certificates): string
    {
        throw new \Exception('Cannot provide proper pkcs7 verification');
    }
}
