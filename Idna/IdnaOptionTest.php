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

final class IdnaOptionTest extends TestCase
{
    public function testItCanBeInstantiatedFromBytes(): void
    {
        $options = IdnaOption::fromBytes(22);
        self::assertSame([
            'USE_STD3_RULES',
            'CHECK_BIDI',
            'NONTRANSITIONAL_TO_ASCII',
        ], $options->list());

        self::assertSame(
            [
            'USE_STD3_RULES',
            'NONTRANSITIONAL_TO_ASCII',
        ],
            $options
            ->ignoreBidi()
            ->disallowUnassigned()
            ->ignoreContextO()
            ->nonTransitionalToAscii()
            ->list()
        );
    }

    public function testItCanBeConvertedToBytes(): void
    {
        $option = IdnaOption::new()
            ->allowUnassigned()
            ->checkContextO()
            ->transitionalToUnicode()
            ->transitionalToAscii()
            ->ignoreContextJ()
            ->prohibitSTD3Rules()
            ->toBytes();

        self::assertSame(65, $option);
    }
}
