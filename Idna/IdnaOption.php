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

use ReflectionClass;
use ReflectionClassConstant;

/**
 * @see https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/uidna_8h.html
 */
final class IdnaOption
{
    private const DEFAULT                    = 0;
    private const ALLOW_UNASSIGNED           = 1;
    private const USE_STD3_RULES             = 2;
    private const CHECK_BIDI                 = 4;
    private const CHECK_CONTEXTJ             = 8;
    private const NONTRANSITIONAL_TO_ASCII   = 0x10;
    private const NONTRANSITIONAL_TO_UNICODE = 0x20;
    private const CHECK_CONTEXTO             = 0x40;


    private function __construct(private readonly int $value)
    {
    }

    private static function cases(): array
    {
        static $assoc;
        if (null === $assoc) {
            $assoc = [];
            $fooClass = new ReflectionClass(self::class);
            foreach ($fooClass->getConstants(ReflectionClassConstant::IS_PRIVATE) as $name => $value) {
                $assoc[$name] = $value;
            }
        }

        return $assoc;
    }

    public static function new(): self
    {
        return new self(self::DEFAULT);
    }

    public static function fromBytes(int $bytes): self
    {
        return new self(array_reduce(
            self::cases(),
            fn (int $value, int $option) => 0 !== ($option & $bytes) ? ($value | $option) : $value,
            self::DEFAULT
        ));
    }

    public static function forIDNA2008Ascii(): self
    {
        return self::new()
            ->nonTransitionalToAscii()
            ->checkBidi()
            ->useSTD3Rules()
            ->checkContextJ();
    }

    public static function forIDNA2008Unicode(): self
    {
        return self::new()
            ->nonTransitionalToUnicode()
            ->checkBidi()
            ->useSTD3Rules()
            ->checkContextJ();
    }

    public function toBytes(): int
    {
        return $this->value;
    }

    /** array<string, int> */
    public function list(): array
    {
        return array_keys(array_filter(
            self::cases(),
            fn (int $value) => 0 !== ($value & $this->value)
        ));
    }

    public function allowUnassigned(): self
    {
        return new self($this->value | self::ALLOW_UNASSIGNED);
    }

    public function disallowUnassigned(): self
    {
        return new self($this->value & ~self::ALLOW_UNASSIGNED);
    }

    public function useSTD3Rules(): self
    {
        return new self($this->value | self::USE_STD3_RULES);
    }

    public function prohibitSTD3Rules(): self
    {
        return new self($this->value & ~self::USE_STD3_RULES);
    }

    public function checkBidi(): self
    {
        return new self($this->value | self::CHECK_BIDI);
    }

    public function ignoreBidi(): self
    {
        return new self($this->value & ~self::CHECK_BIDI);
    }

    public function checkContextJ(): self
    {
        return new self($this->value | self::CHECK_CONTEXTJ);
    }

    public function ignoreContextJ(): self
    {
        return new self($this->value & ~self::CHECK_CONTEXTJ);
    }

    public function checkContextO(): self
    {
        return new self($this->value | self::CHECK_CONTEXTO);
    }

    public function ignoreContextO(): self
    {
        return new self($this->value & ~self::CHECK_CONTEXTO);
    }

    public function nonTransitionalToAscii(): self
    {
        return new self($this->value | self::NONTRANSITIONAL_TO_ASCII);
    }

    public function transitionalToAscii(): self
    {
        return new self($this->value & ~self::NONTRANSITIONAL_TO_ASCII);
    }

    public function nonTransitionalToUnicode(): self
    {
        return new self($this->value | self::NONTRANSITIONAL_TO_UNICODE);
    }

    public function transitionalToUnicode(): self
    {
        return new self($this->value & ~self::NONTRANSITIONAL_TO_UNICODE);
    }
}
