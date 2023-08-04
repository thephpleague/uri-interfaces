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

final class ResultTest extends TestCase
{
    public function testItCanBeInstantiatedFromArray(): void
    {
        $infos = ['result' => '', 'isTransitionalDifferent' => false, 'errors' => 0];
        $result = Result::fromIntl($infos);

        self::assertSame('', $result->domain());
        self::assertFalse($result->isTransitionalDifferent());
        self::assertCount(0, $result->errors());
        self::assertFalse($result->hasErrors());
        self::assertFalse($result->hasError(Error::DISALLOWED));
        self::assertFalse($result->hasError(Error::BIDI));
    }

    public function testInvalidSyntaxAfterIDNConversion(): void
    {
        $result = Converter::toAscii('％００.com', Option::forIDNA2008Ascii());

        self::assertTrue($result->hasErrors());
        self::assertCount(1, $result->errors());
        self::assertSame(Error::DISALLOWED, $result->errors()[0]);
        self::assertTrue($result->hasError(Error::DISALLOWED));
        self::assertFalse($result->hasError(Error::BIDI));
    }
}
