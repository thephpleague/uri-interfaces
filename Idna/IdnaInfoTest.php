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

        self::assertSame('', $result->result());
        self::assertFalse($result->isTransitionalDifferent());
        self::assertCount(0, $result->errorList());
        self::assertFalse($result->hasErrors());
    }

    public function testInvalidSyntaxAfterIDNConversion(): void
    {
        $result = Idna::toAscii('％００.com', IdnaOption::forIDNA2008Ascii());

        self::assertTrue($result->hasErrors());
        self::assertCount(1, $result->errorList());
        self::assertSame(IdnaError::DISALLOWED, $result->errorList()[0]);
    }
}
