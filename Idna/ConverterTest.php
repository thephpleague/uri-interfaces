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

namespace League\Uri\Idna;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stringable;

#[CoversClass(Converter::class)]
final class ConverterTest extends TestCase
{
    #[DataProvider('invalidDomainProvider')]
    public function testToAsciiThrowsException(string $domain): void
    {
        self::assertNotEmpty(Converter::toAscii($domain)->errors());
    }

    /**
     * @return iterable<string,array{0:string}>
     */
    public static function invalidDomainProvider(): iterable
    {
        return [
            'invalid IDN domain' => ['a⒈com'],
            'invalid IDN domain full size' => ['％００.com'],
            'invalid IDN domain full size rawurlencode ' => ['%ef%bc%85%ef%bc%94%ef%bc%91.com'],
            'invalid character in host ' => ['to to.com'],
            'contains disallow character' => ['bébé.'.str_repeat('A', 70).'.'.str_repeat('A', 200)],
        ];
    }

    public function testToUnicodeThrowsException(): void
    {
        self::assertNotEmpty(Converter::toUnicode('xn--a-ecp.ru')->errors());
    }

    #[DataProvider('toUnicodeProvider')]
    public function testToIDN(Stringable|string $domain, string $expectedDomain): void
    {
        self::assertSame($expectedDomain, Converter::toUnicode($domain)->domain());
    }

    /**
     * @return iterable<string,array{domain:Stringable|string, expectedDomain:string}>
     */
    public static function toUnicodeProvider(): iterable
    {
        return [
            'simple domain' => [
                'domain' => 'www.ulb.ac.be',
                'expectedDomain' => 'www.ulb.ac.be',
            ],
            'ASCII to IDN domain' => [
                'domain' => 'www.xn--85x722f.xn--55qx5d.cn',
                'expectedDomain' => 'www.食狮.公司.cn',
            ],
            'IDN to IDN domain' => [
                'domain' => new class () implements Stringable {
                    public function __toString(): string
                    {
                        return 'www.食狮.公司.cn';
                    }
                },
                'expectedDomain' => 'www.食狮.公司.cn',
            ],
            'empty string domain and null suffix' => [
                'domain' => '',
                'expectedDomain' => '',
            ],
            'domain with null suffix' => [
                'domain' => 'www.xn--85x722f.xn--55qx5d.cn',
                'expectedDomain' => 'www.食狮.公司.cn',
            ],
            'domain with URLencoded data' => [
                'domain' => 'b%C3%A9b%C3%A9.be',
                'expectedDomain' => 'bébé.be',
            ],
        ];
    }

    #[DataProvider('toAsciiProvider')]
    public function testToAscii(Stringable|string $domain, string $expectedDomain): void
    {
        self::assertSame($expectedDomain, Converter::toAscii($domain)->domain());
    }

    /**
     * @return iterable<string,array{domain:Stringable|string, expectedDomain:string}>
     */
    public static function toAsciiProvider(): iterable
    {
        return [
            'simple domain' => [
                'domain' => 'www.ulb.ac.be',
                'expectedDomain' => 'www.ulb.ac.be',
            ],
            'simple domain with root label' => [
                'domain' => 'www.ulb.ac.be.',
                'expectedDomain' => 'www.ulb.ac.be.',
            ],
            'ASCII to ASCII domain' => [
                'domain' => new class () implements Stringable {
                    public function __toString(): string
                    {
                        return 'www.xn--85x722f.xn--55qx5d.cn';
                    }
                },
                'expectedDomain' => 'www.xn--85x722f.xn--55qx5d.cn',
            ],
            'ASCII to IDN domain' => [
                'domain' => 'www.食狮.公司.cn',
                'expectedDomain' => 'www.xn--85x722f.xn--55qx5d.cn',
            ],
        ];
    }

    public function testExceptionThrownOnConversionToAsciiIfTheDomainIsTooLong(): void
    {
        self::assertNotEmpty(Converter::toAscii(str_repeat('A', 255))->errors());
    }

    public function testExceptionThrownOnConversionToAsciiIfTheDomainLabelIsTooLong(): void
    {
        self::assertNotEmpty(Converter::toAscii('aa'.str_repeat('A', 64))->errors());
    }

    #[DataProvider('provideIDNUri')]
    public function testHostIsIDN(Stringable|string|null $host, bool $expected): void
    {
        self::assertSame($expected, Converter::isIdn($host));
    }

    public static function provideIDNUri(): iterable
    {
        yield 'ascii host' => [
            'host' => new class () implements Stringable {
                public function __toString(): string
                {
                    return 'www.example.com';
                }
            },
            'expected' => false,
        ];

        yield 'ascii host with invalid converted i18n' => [
            'host' => 'www.xn--ample.com',
            'expected' => false,
        ];

        yield 'idn host' => [
            'host' => 'www.bébé.be',
            'expected' => true,
        ];

        yield 'empty host' => [
            'host' => '',
            'expected' => false,
        ];

        yield 'null host' => [
            'host' => null,
            'expected' => false,
        ];

        yield 'idn host with a valid conversion result' => [
            'host' => Converter::toAsciiOrFail('www.bébé.be'),
            'expected' => true,
        ];

        yield 'idn host with an invalid conversion result' => [
            'host' => 'www.％００.com',
            'expected' => false,
        ];

        yield 'idn host with URL encoded characters' => [
            'host' => 'b%C3%A9b%C3%A9.be',
            'expected' => true,
        ];
    }
}
