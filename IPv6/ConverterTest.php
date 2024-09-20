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

namespace League\Uri\IPv6;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ValueError;

final class ConverterTest extends TestCase
{
    #[DataProvider('ipv6NormalizationUriProvider')]
    public function testItCanExpandOrCompressTheHost(
        string $ipv6,
        string $ipv6Compressed,
        string $ipv6Expanded,
    ): void {

        self::assertSame($ipv6Compressed, Converter::compress($ipv6));
        self::assertSame($ipv6Expanded, Converter::expand($ipv6));
    }

    public static function ipv6NormalizationUriProvider(): iterable
    {
        yield 'no change happen with a non IP ipv6' => [
            'ipv6' => 'example.com',
            'ipv6Compressed' => 'example.com',
            'ipv6Expanded' => 'example.com',
        ];

        yield 'no change happen with a IPv4 ipv6' => [
            'ipv6' => '127.0.0.1',
            'ipv6Compressed' => '127.0.0.1',
            'ipv6Expanded' => '127.0.0.1',
        ];

        yield 'IPv6 gets expanded if needed' => [
            'ipv6' => '[fe80::a%25en1]',
            'ipv6Compressed' => '[fe80::a%25en1]',
            'ipv6Expanded' => '[fe80:0000:0000:0000:0000:0000:0000:000a%25en1]',
        ];

        yield 'IPv6 gets compressed if needed' => [
            'ipv6' => '[0000:0000:0000:0000:0000:0000:0000:0001]',
            'ipv6Compressed' => '[::1]',
            'ipv6Expanded' => '[0000:0000:0000:0000:0000:0000:0000:0001]',
        ];
    }

    #[DataProvider('invalidIpv6')]
    public function testItFailsToCompressANonIpv6(string $invalidIp): void
    {
        $this->expectException(ValueError::class);

        Converter::compressIp($invalidIp);
    }

    #[DataProvider('invalidIpv6')]
    public function testItFailsToExpandANonIpv6(string $invalidIp): void
    {
        $this->expectException(ValueError::class);

        Converter::expandIp($invalidIp);
    }

    public static function invalidIpv6(): iterable
    {
        yield 'hostname' => ['invalidIp' => 'example.com'];

        yield 'ip future' => ['invalidIp' => '[v42.fdfsffd]'];

        yield 'IPv6 with zoneIdentifier' => ['invalidIp' => 'fe80::a%25en1'];
    }
}
