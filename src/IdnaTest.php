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

use League\Uri\Exceptions\IdnaConversionFailed;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \League\Uri\Idna
 */
final class IdnaTest extends TestCase
{
    /**
     * @dataProvider invalidDomainProvider
     */
    public function testToAsciiThrowsException(string $domain): void
    {
        $this->expectException(IdnaConversionFailed::class);

        Idna::toAscii($domain, Idna::IDNA2008_ASCII);
    }

    /**
     * @return iterable<string,array{0:string}>
     */
    public function invalidDomainProvider(): iterable
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
        $this->expectException(IdnaConversionFailed::class);

        Idna::toUnicode('xn--a-ecp.ru', Idna::IDNA2008_UNICODE);
    }

    /**
     * @dataProvider toUnicodeProvider
     */
    public function testToIDN(string $domain, string $expectedDomain): void
    {
        self::assertSame(
            $expectedDomain,
            Idna::toUnicode($domain, Idna::IDNA2008_UNICODE)->result()
        );
    }

    /**
     * @return iterable<string,array{domain:string, expectedDomain:string}>
     */
    public function toUnicodeProvider(): iterable
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
        self::assertSame(
            $expectedDomain,
            Idna::toAscii($domain, Idna::IDNA2008_ASCII)->result()
        );
    }

    /**
     * @return iterable<string,array{domain:string, expectedDomain:string}>
     */
    public function toAsciiProvider(): iterable
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
        $this->expectException(IdnaConversionFailed::class);

        Idna::toAscii(str_repeat('A', 255), Idna::IDNA2008_ASCII);
    }

    public function testExceptionThrownOnConversionToAsciiIfTheDomainLabelIsTooLong(): void
    {
        $this->expectException(IdnaConversionFailed::class);

        Idna::toAscii('aa'.str_repeat('A', 64), Idna::IDNA2008_ASCII);
    }
}
