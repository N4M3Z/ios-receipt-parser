<?php

namespace Proton\IosReceiptParser\ASN1;

use Symfony\Component\Process\Process;

final class OpenSslProcessPkcs7Reader implements Pkcs7Reader
{
    public function __construct()
    {
        if (!class_exists(Process::class)) {
            throw new \RuntimeException('You need to install symfony/process to use ' . self::class);
        }
    }

    public function readUnverified(string $ber): string
    {
        return (new Process([
            'openssl',
            'cms',
            '-verify',
           '-noverify',
            // Openssl DER parsing is permissive enough to allow for BER input
            '-inform', 'der',
        ]))
            ->setInput($ber)
            ->mustRun()
            ->getOutput();
    }

    public function readUsingOnlyTrustedCerts(string $ber, string ...$certificates): string
    {
        $args = [
            'openssl',
            'cms',
            '-verify',
            // Ignore whatever certificate was inside
            '-nointern',
            // Since we're supplying our own certificates (which are likely to be self-signed) and ignoring bundled,
            // there is no need to verify the certificate's signatures
            '-noverify',
            // Openssl DER parsing is permissive enough to allow for BER input
            '-inform', 'der',
        ];

        $tmpfiles = [];

        foreach ($certificates as $certificate) {
            if (strlen($certificate) > 255) {
                $tmpfile = tempnam(sys_get_temp_dir(), 'cert');
                file_put_contents($tmpfile, $certificate);

                $tmpfiles[] = $certificate = $tmpfile;
            }

            $args[] = '-certfile';
            $args[] = $certificate;
        }

        try {
            return (new Process($args))
                ->setInput($ber)
                ->mustRun()
                ->getOutput();
        } finally {
            foreach ($tmpfiles as $tmpfile) {
                @unlink($tmpfile);
            }
        }
    }
}
