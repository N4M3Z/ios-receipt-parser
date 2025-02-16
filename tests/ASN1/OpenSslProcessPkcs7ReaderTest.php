<?php

namespace ASN1;

use Proton\IosReceiptParser\ASN1\OpenSslProcessPkcs7Reader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;

class OpenSslProcessPkcs7ReaderTest extends TestCase
{
    private const RECEIPT = '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';
    private const EXPECTED_PAYLOAD = 'MYIDHjAPAgEAAgEBBAcMBVhjb2RlMAsCAQECAQEEAwIBADAhAgECAgEBBBkMF2NvbS5mbGludGNhc3QuZXZlcm1hdGNoMAwCAQMCAQEEBAwCNTcwEAIBBAIBAQQIk/f9vwYAAAAwHAIBBQIBAQQUJJC06zmDpPR6GRKxKd2Em0rVUAMwCgIBCAIBAQQCFgAwIgIBDAIBAQQaFhgyMDIwLTA5LTAxVDE2OjU4OjQ5KzAzMDAwgdICARECAQEEgckxgcYwDAICBqUCAQEEAwIBATAbAgIGpgIBAQQSDBBydS5zdWIudmlwLndlZWsyMA0CAganAgEBBAQMAjU3MCMCAgaoAgEBBBoWGDIwMjAtMDktMDFUMTY6NTg6NDIrMDMwMDANAgIGqQIBAQQEDAI1NjAjAgIGqgIBAQQaFhgyMDIwLTA5LTAxVDE2OjU4OjM1KzAzMDAwIwICBqwCAQEEGhYYMjAyMC0wOS0wMVQxNjo1ODo0OSswMzAwMAwCAga3AgEBBAMCAQAwgdICARECAQEEgckxgcYwDAICBqUCAQEEAwIBATAbAgIGpgIBAQQSDBBydS5zdWIudmlwLm1vbnRoMA0CAganAgEBBAQMAjU4MCMCAgaoAgEBBBoWGDIwMjAtMDktMDFUMTY6NTg6NDkrMDMwMDANAgIGqQIBAQQEDAI1NjAjAgIGqgIBAQQaFhgyMDIwLTA5LTAxVDE2OjU4OjM1KzAzMDAwIwICBqwCAQEEGhYYMjAyMC0wOS0wMVQxNjo1OToxOSswMzAwMAwCAga3AgEBBAMCAQAwgZ4CARECAQEEgZUxgZIwDAICBqUCAQEEAwIBATAbAgIGpgIBAQQSDBBydS5zdWIudmlwLndlZWsyMA0CAganAgEBBAQMAjU2MCMCAgaoAgEBBBoWGDIwMjAtMDktMDFUMTY6NTg6MzUrMDMwMDAjAgIGrAIBAQQaFhgyMDIwLTA5LTAxVDE2OjU4OjQyKzAzMDAwDAICBrcCAQEEAwIBADAiAgEVAgEBBBoWGDQwMDEtMDEtMDFUMDM6MDA6MDArMDMwMA==';
    private const CERTIFICATE = <<<PEM
-----BEGIN CERTIFICATE-----
MIIDdDCCAlygAwIBAgIBATANBgkqhkiG9w0BAQsFADBfMREwDwYDVQQDDAhTdG9y
ZUtpdDERMA8GA1UECgwIU3RvcmVLaXQxETAPBgNVBAsMCFN0b3JlS2l0MQswCQYD
VQQGEwJVUzEXMBUGCSqGSIb3DQEJARYIU3RvcmVLaXQwHhcNMjAwNDAxMTc1MjM1
WhcNNDAwMzI3MTc1MjM1WjBfMREwDwYDVQQDDAhTdG9yZUtpdDERMA8GA1UECgwI
U3RvcmVLaXQxETAPBgNVBAsMCFN0b3JlS2l0MQswCQYDVQQGEwJVUzEXMBUGCSqG
SIb3DQEJARYIU3RvcmVLaXQwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB
AQDbf5A8LHMP25cmS5O7CvihIT7IYdkkyF4fdT7ak9sxGpGAub/lDMs8uw5EYib6
BCm2Sedv4BvmDWjNJW7Ddgj1SguuenQ8xKkLs89iD/u0vPfbhF4o60cN8e2LrPWf
sAk4o257yyZQChrhidFydgs5TMtPbsCzX7eVurmoXUp0q+9vQaV+CY26PT3NcFfY
7e/V2nfIkwQc7wmIeGXOgfKNcucHGm4mEvcysQ27OJBrBsT8DeWVUM2RyLol9FjJ
jOFx20pF8y0ZlgNWgaZE7nV3W1PPeKxduj5fUCtcKYzdwtcqF98itNfkeKivqG2n
wdpoLWbMzykLUCzjwvvmXxLBAgMBAAGjOzA5MA8GA1UdEwEB/wQFMAMBAf8wDgYD
VR0PAQH/BAQDAgKEMBYGA1UdJQEB/wQMMAoGCCsGAQUFBwMDMA0GCSqGSIb3DQEB
CwUAA4IBAQCyAOA88ejpYr3A1h1Anle5OJB3dlLSqEtwbrhnmfuzilWf7x0ouF8q
0XOfNUc3u0bTdhDy8GnszWKZcflgioRIOMS9i2cluatsM2Wt2MKaeEgP6czBJw3G
z2Q8bYBZM4zKNgYqERuNSc4I/2bARyhL61rBKwlWLKWqCQN7MjHc6IV4SM7AxRIR
ag8Mri8Fym96ZH8gLHXmTLES0/3jH14NfbhY16B85H9jq5eaK8Mq2NCy4dVaDTkb
b2coqRKD1od4bZm9XrMK4JjO9urDjm1p67dAgT2HPXBR0cRdjaXcf2pYGt5gdjdS
7P+sGV0MFS+KD/WJyNcrHR7sK5EFpz1P
-----END CERTIFICATE-----
PEM;

    /**
     * @dataProvider readerProvider
     */
    public function testReadUsingOnlyTrustedCertsWithoutRequiredCertificates(OpenSslProcessPkcs7Reader $reader): void
    {
        $this->expectException(ProcessFailedException::class);
        $reader->readUsingOnlyTrustedCerts(base64_decode(self::RECEIPT));
    }

    /**
     * @dataProvider readerProvider
     */
    public function testReadUsingOnlyTrustedCerts(OpenSslProcessPkcs7Reader $reader): void
    {
        $payload = $reader->readUsingOnlyTrustedCerts(base64_decode(self::RECEIPT), self::CERTIFICATE);
        $this->assertSame(self::EXPECTED_PAYLOAD, base64_encode($payload));
    }

    /**
     * @dataProvider readerProvider
     */
    public function testReadUnverified(OpenSslProcessPkcs7Reader $reader): void
    {
        $payload = $reader->readUnverified(base64_decode(self::RECEIPT));
        $this->assertSame(self::EXPECTED_PAYLOAD, base64_encode($payload));
    }

    public function readerProvider(): array
    {
        return [
            [new OpenSslProcessPkcs7Reader()],
        ];
    }
}
