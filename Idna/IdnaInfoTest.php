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

final class IdnaInfoTest extends TestCase
{
    public function testItCanBeInstantiatedFromArray(): void
    {
        $infos = ['result' => '', 'isTransitionalDifferent' => false, 'errors' => 0];
        $result = IdnaInfo::fromIntl($infos);

        self::assertSame('', $result->domain());
        self::assertFalse($result->isTransitionalDifferent());
        self::assertCount(0, $result->errors());
        self::assertFalse($result->hasErrors());
        self::assertFalse($result->hasError(IdnaError::DISALLOWED));
        self::assertFalse($result->hasError(IdnaError::BIDI));
        self::assertSame(IdnaError::NONE->value, $result->errorsAsBytes());
    }

    public function testInvalidSyntaxAfterIDNConversion(): void
    {
        $result = Idna::toAscii('％００.com', IdnaOption::forIDNA2008Ascii());

        self::assertTrue($result->hasErrors());
        self::assertCount(1, $result->errors());
        self::assertSame(IdnaError::DISALLOWED, $result->errors()[0]);
        self::assertTrue($result->hasError(IdnaError::DISALLOWED));
        self::assertFalse($result->hasError(IdnaError::BIDI));
        self::assertSame(IdnaError::DISALLOWED->value, $result->errorsAsBytes());
    }
}
