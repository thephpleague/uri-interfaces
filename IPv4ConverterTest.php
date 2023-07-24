<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use PHPUnit\Framework\TestCase;
use function extension_loaded;
use const PHP_INT_SIZE;

/**
 * @coversDefaultClass \League\Uri\IPv4Converter
 */
final class IPv4ConverterTest extends TestCase
{
    /**
     * @dataProvider providerHost
     * @param ?string $input
     * @param ?string $expected
     */
    public function testParseWithAutoDetectCalculator(?string $input, ?string $expected): void
    {
        if (!extension_loaded('gmp') && !extension_loaded('bcmath') && 4 >= PHP_INT_SIZE) {
            self::markTestSkipped('The PHP must be compile for a x64 OS or loads the GMP or the BCmath extension.');
        }

        self::assertEquals($expected, IPv4Converter::fromEnvironment()->normalize($input) ?? $input);
    }

    /**
     * @dataProvider providerHost
     * @param ?string $input
     * @param ?string $expected
     */
    public function testParseWithGMPCalculator(?string $input, ?string $expected): void
    {
        if (!extension_loaded('gmp')) {
            self::markTestSkipped('The GMP extension is needed to execute this test.');
        }

        self::assertEquals($expected, IPv4Converter::fromGMP()->normalize($input) ?? $input);
    }

    /**
     * @dataProvider providerHost
     * @param ?string $input
     * @param ?string $expected
     */
    public function testParseWithNativeCalculator(?string $input, ?string $expected): void
    {
        if (4 > PHP_INT_SIZE) {  /* @phpstan-ignore-line */
            self::markTestSkipped('The PHP must be compile for a x64 OS.');
        }

        self::assertEquals($expected, IPv4Converter::fromNative()->normalize($input) ?? $input);
    }

    /**
     * @dataProvider providerHost
     * @param ?string $input
     * @param ?string $expected
     */
    public function testParseWithBCMathCalculator(?string $input, ?string $expected): void
    {
        if (!extension_loaded('bcmath')) {
            self::markTestSkipped('The PHP must be compile with Bcmath extension enabled.');
        }

        self::assertEquals($expected, IPv4Converter::fromBCMath()->normalize($input) ?? $input);
    }

    public static function providerHost(): array
    {
        return [
            'null host' => [null, null],
            'non ip host' => ['ulb.ac.be', 'ulb.ac.be'],
            'empty host' => ['', ''],
            '0 host' => ['0', '0.0.0.0'],
            'normal IP' => ['192.168.0.1', '192.168.0.1'],
            'normal IP ending with a dot' => ['192.168.0.1.', '192.168.0.1'],
            'octal (1)' => ['030052000001', '192.168.0.1'],
            'octal (2)' => ['0300.0250.0000.0001', '192.168.0.1'],
            'hexadecimal (1)' => ['0x', '0.0.0.0'],
            'hexadecimal (2)' => ['0xffffffff', '255.255.255.255'],
            'decimal (1)' => ['3232235521', '192.168.0.1'],
            'decimal (2)' => ['999999999', '59.154.201.255'],
            'decimal (3)' => ['256', '0.0.1.0'],
            'decimal (4)' => ['192.168.257', '192.168.1.1'],
            'invalid host (0)' => ['256.256.256.256.256', '256.256.256.256.256'],
            'invalid host (1)' => ['256.256.256.256', '256.256.256.256'],
            'invalid host (3)' => ['256.256.256', '256.256.256'],
            'invalid host (4)' => ['999999999.com', '999999999.com'],
            'invalid host (5)' => ['10000000000', '10000000000'],
            'invalid host (6)' => ['192.168.257.com', '192.168.257.com'],
            'invalid host (7)' => ['192..257', '192..257'],
            'invalid host (8)' => ['0foobar', '0foobar'],
            'invalid host (9)' => ['0xfoobar', '0xfoobar'],
            'invalid host (10)' => ['0xffffffff1', '0xffffffff1'],
            'invalid host (11)' => ['0300.5200.0000.0001', '0300.5200.0000.0001'],
            'invalid host (12)' => ['255.255.256.255', '255.255.256.255'],
            'invalid host (13)' => ['0ffaed', '0ffaed'],
            'invalid host (14)' => ['192.168.1.0x3000000', '192.168.1.0x3000000'],
        ];
    }
}
