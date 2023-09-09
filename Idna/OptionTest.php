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

use const IDNA_CHECK_BIDI;
use const IDNA_NONTRANSITIONAL_TO_ASCII;
use const IDNA_USE_STD3_RULES;

final class OptionTest extends TestCase
{
    public function testItCanBeInstantiatedFromBytes(): void
    {
        $options = Option::new(22);
        $altOptions = $options
            ->ignoreBidi()
            ->disallowUnassigned()
            ->ignoreContextO()
            ->nonTransitionalToAscii();

        self::assertSame([
            'USE_STD3_RULES',
            'CHECK_BIDI',
            'NONTRANSITIONAL_TO_ASCII',
        ], $options->list());

        self::assertSame([
            'USE_STD3_RULES',
            'NONTRANSITIONAL_TO_ASCII',
        ], $altOptions->list());
    }

    public function testItCanAddOrRemoveOption(): void
    {
        $options = Option::new()
            ->add(IDNA_USE_STD3_RULES)
            ->add(Option::new()->checkBidi())
            ->add()
            ->add(IDNA_NONTRANSITIONAL_TO_ASCII);
        $altOptions = $options
            ->remove(IDNA_CHECK_BIDI)
            ->remove(Option::new()->disallowUnassigned())
            ->remove();

        self::assertSame([
            'USE_STD3_RULES',
            'CHECK_BIDI',
            'NONTRANSITIONAL_TO_ASCII',
        ], $options->list());

        self::assertSame([
            'USE_STD3_RULES',
            'NONTRANSITIONAL_TO_ASCII',
        ], $altOptions->list());
    }

    public function testItCanBeConvertedToBytes(): void
    {
        $option = Option::new()
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
