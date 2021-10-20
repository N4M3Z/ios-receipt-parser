<?php

namespace Proton\IosReceiptParser\ASN1;

interface Pkcs7Reader
{
    /**
     * Read data from pkcs#7 container trusting any certificate bundled inside.
     *
     * @param string $ber BER-encoded (NOT guaranteed to be DER-encoded) pkcs#7 container
     * @return string The binary data
     */
    public function readUnverified(string $ber): string;

    /**
     * Ignore any bundled certificates and verify pkcs#7 container using only the provided certificates
     * which themselves will no be verified.
     *
     * @param string $ber BER-encoded (NOT guaranteed to be DER-encoded) pkcs#7 container
     * @param string ...$certificates Trusted (will not be verified) certificates
     * (pem-encoded or paths to pem-encoded files) to search signer's certificate amongst
     * @return string The binary data after verifications
     */
    public function readUsingOnlyTrustedCerts(string $ber, string ...$certificates): string;
}
