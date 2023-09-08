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

namespace League\Uri\KeyValuePair;

use PHPUnit\Framework\TestCase;

final class ConverterTest extends TestCase
{
    public function testItWilltConvertPairsToStringOrNull(): void
    {
        $converterWithAmpersand = Converter::new('&');
        self::assertSame('&', $converterWithAmpersand->separator());
        self::assertSame([], $converterWithAmpersand->encodingMap());
        self::assertSame('a=b', $converterWithAmpersand->toValue([['a', 'b']]));
        self::assertSame('a=b&b=c', $converterWithAmpersand->toValue([['a', 'b'], ['b', 'c']]));
        self::assertSame('a=&b', $converterWithAmpersand->toValue([['a', ''], ['b', null]]));
        self::assertSame('=&=', $converterWithAmpersand->toValue([['', ''], ['', '']]));
        self::assertSame('a==', $converterWithAmpersand->toValue([['a', '=']]));
        self::assertSame('&&', $converterWithAmpersand->toValue([['', null], ['', null], ['', null]]));
        self::assertNull($converterWithAmpersand->toValue([]));

        $converterWithSemiColon = Converter::new(';');
        self::assertSame(';', $converterWithSemiColon->separator());
        self::assertSame([], $converterWithSemiColon->encodingMap());
        self::assertSame('a=b', $converterWithSemiColon->toValue([['a', 'b']]));
        self::assertSame('a=b;b=c', $converterWithSemiColon->toValue([['a', 'b'], ['b', 'c']]));
        self::assertSame('a=;b', $converterWithSemiColon->toValue([['a', ''], ['b', null]]));
        self::assertSame('=;=', $converterWithSemiColon->toValue([['', ''], ['', '']]));
        self::assertSame('a==', $converterWithSemiColon->toValue([['a', '=']]));
        self::assertNull($converterWithSemiColon->toValue([]));
    }

    public function testItWillConvertPairsAndMapCharactersIfNeeded(): void
    {
        $converter = Converter::new('&')->withEncodingMap(['FOO' => 'bar']);
        self::assertSame('&', $converter->separator());
        self::assertSame(['FOO' => 'bar'], $converter->encodingMap());
        self::assertSame('toto=bar', $converter->toValue([['toto', 'FOO']]));
    }

    public function testEncodingDecodingMapDiffersWithEachSpecification(): void
    {
        $encoded = [['tata%2A%3F%2F%20_%2B', 'tata%2A%3F%2F%20_%2B']];

        $rfc3986 = Converter::fromRFC3986();
        self::assertSame([], $rfc3986->encodingMap());
        self::assertSame('tata%2A%3F%2F%20_%2B=tata%2A%3F%2F%20_%2B', $rfc3986->toValue($encoded));
        self::assertSame($encoded, $rfc3986->toPairs('tata%2A%3F%2F%20_%2B=tata%2A%3F%2F%20_%2B'));

        $rfc1738 = Converter::fromRFC1738();
        self::assertSame(['%20' => '+'], $rfc1738->encodingMap());
        self::assertSame('tata%2A%3F%2F+_%2B=tata%2A%3F%2F+_%2B', $rfc1738->toValue($encoded));
        self::assertSame($encoded, $rfc1738->toPairs('tata%2A%3F%2F+_%2B=tata%2A%3F%2F+_%2B'));

        $formData = Converter::fromFormData();
        self::assertSame(['%20' => '+', '%2A' => '*'], $formData->encodingMap());
        self::assertSame('tata*%3F%2F+_%2B=tata*%3F%2F+_%2B', $formData->toValue($encoded));
        self::assertSame($encoded, $formData->toPairs('tata*%3F%2F+_%2B=tata*%3F%2F+_%2B'));
    }

    public function testEncodingValueByType(): void
    {
        self::assertSame(
            'string=string;true=1;false=0;integer=42;float=42.0;null',
            Converter::new(';')->toValue([
                ['string', 'string'],
                ['true', true],
                ['false', false],
                ['integer', 42],
                ['float', 42.000000000],
                ['null', null],
            ])
        );
    }

    public function testDecodingValue(): void
    {
        self::assertSame(
            [
                ['string', 'string'],
                ['true', '1'],
                ['false', '0'],
                ['integer', '42'],
                ['float', '42.000000000'],
                ['null', null],
            ],
            Converter::new(';')->toPairs('string=string;true=1;false=0;integer=42;float=42.000000000;null')
        );
    }

    public function testDecodingByType(): void
    {
        $converter = Converter::new(';');
        self::assertSame([], $converter->toPairs(null));
        self::assertSame([['', null]], $converter->toPairs(''));
        self::assertSame([['42', null]], $converter->toPairs(42));
        self::assertSame([['42', null]], $converter->toPairs(42.000));
        self::assertSame([['0', null]], $converter->toPairs(false));
        self::assertSame([['1', null]], $converter->toPairs(true));
        self::assertSame([['string', null]], $converter->toPairs('string'));
    }
}
