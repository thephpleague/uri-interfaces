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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \League\Uri\Idna\Idna
 */
final class IdnaTest extends TestCase
{
    /**
     * @dataProvider invalidDomainProvider
     */
    public function testToAsciiThrowsException(string $domain): void
    {
        self::assertNotEmpty(Idna::toAscii($domain, IdnaOption::forIDNA2008Ascii())->errors());
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
        self::assertNotEmpty(Idna::toUnicode('xn--a-ecp.ru', IdnaOption::forIDNA2008Unicode())->errors());
    }

    /**
     * @dataProvider toUnicodeProvider
     */
    public function testToIDN(string $domain, string $expectedDomain): void
    {
        self::assertSame($expectedDomain, Idna::toUnicode($domain, IdnaOption::forIDNA2008Unicode())->result());
    }

    /**
     * @return iterable<string,array{domain:string, expectedDomain:string}>
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
                'domain' => 'www.食狮.公司.cn',
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

    /**
     * @dataProvider toAsciiProvider
     */
    public function testToAscii(string $domain, string $expectedDomain): void
    {
        self::assertSame($expectedDomain, Idna::toAscii($domain, IdnaOption::forIDNA2008Ascii())->result());
    }

    /**
     * @return iterable<string,array{domain:string, expectedDomain:string}>
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
                'domain' => 'www.xn--85x722f.xn--55qx5d.cn',
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
        self::assertNotEmpty(Idna::toAscii(str_repeat('A', 255), IdnaOption::forIDNA2008Ascii())->errors());
    }

    public function testExceptionThrownOnConversionToAsciiIfTheDomainLabelIsTooLong(): void
    {
        self::assertNotEmpty(Idna::toAscii('aa'.str_repeat('A', 64), IdnaOption::forIDNA2008Ascii())->errors());
    }
}
