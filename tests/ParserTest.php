<?php

use Proton\IosReceiptParser\ASN1\OpenSslProcessPkcs7Reader;
use Proton\IosReceiptParser\ASN1\Pkcs7UnverifiedParser;
use Proton\IosReceiptParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    use \DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

    private const RECEIPT = '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';
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
    private const EXPECTED_DATA = [
        'app_item_id' => 0,
        'bundle_id' => 'com.flintcast.evermatch',
        'application_version' => '57',
        'receipt_creation_date' => '2020-09-01T16:58:49+0300',
        'receipt_creation_date_ms' => '1598968729000',
        'in_app' => [
            [
                'quantity' => '1',
                'product_id' => 'ru.sub.vip.week2',
                'transaction_id' => '57',
                'purchase_date' => '2020-09-01T16:58:42+0300',
                'purchase_date_ms' => '1598968722000',
                'original_transaction_id' => '56',
                'original_purchase_date' => '2020-09-01T16:58:35+0300',
                'original_purchase_date_ms' => '1598968715000',
                'expires_date' => '2020-09-01T16:58:49+0300',
                'expires_date_ms' => '1598968729000'
            ],
            [
                'quantity' => '1',
                'product_id' => 'ru.sub.vip.month',
                'transaction_id' => '58',
                'purchase_date' => '2020-09-01T16:58:49+0300',
                'purchase_date_ms' => '1598968729000',
                'original_transaction_id' => '56',
                'original_purchase_date' => '2020-09-01T16:58:35+0300',
                'original_purchase_date_ms' => '1598968715000',
                'expires_date' => '2020-09-01T16:59:19+0300',
                'expires_date_ms' => '1598968759000'
            ],
            [
                'quantity' => '1',
                'product_id' => 'ru.sub.vip.week2',
                'transaction_id' => '56',
                'purchase_date' => '2020-09-01T16:58:35+0300',
                'purchase_date_ms' => '1598968715000',
                'original_transaction_id' => '56',
                'original_purchase_date' => '2020-09-01T16:58:35+0300',
                'original_purchase_date_ms' => '1598968715000',
                'expires_date' => '2020-09-01T16:58:42+0300',
                'expires_date_ms' => '1598968722000'
            ],
        ],
        'original_application_version' => '57',
        'receipt_expiration_date' => '4001-01-01T03:00:00+0300',
        'receipt_expiration_date_ms' => '64092211200000',      
    ];

    /**
     * @dataProvider parserProvider
     */
    public function testParseUsingOnlyTrustedCerts(Parser $parser): void
    {
        try {
            $receipt = $parser->parseUsingOnlyTrustedCerts(self::RECEIPT, self::CERTIFICATE);
        } catch (\Exception $e) {
            if ($e->getMessage() !== 'Cannot provide proper pkcs7 verification') {
                throw $e;
            }

            $this->markTestSkipped('This parser cannot verify PKCS#7 container');
            return;
        }

        $this->assertSame(self::EXPECTED_DATA, $receipt->jsonSerialize());
    }

    /**
     * @dataProvider parserProvider
     */
    public function testParseUnverified(Parser $parser): void
    {
        $this->assertArraySubset(self::EXPECTED_DATA, $parser->parseUnverified(self::RECEIPT)->jsonSerialize());
    }

    public function parserProvider(): array
    {
        return [
            [new Parser()],
            [new Parser(new Pkcs7UnverifiedParser())],
            [new Parser(new OpenSslProcessPkcs7Reader())],
        ];
    }
}
