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

use League\Uri\Components\FragmentDirectives\TextDirective;
use League\Uri\Components\Query;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Stringable;
use TypeError;
use ValueError;

final class StringCoercionModeTest extends TestCase
{
    #[DataProvider('nativeCoercionModeProvider')]
    public function test_it_can_coerce_using_php_coercion_mode(mixed $input, ?string $expected): void
    {
        self::assertSame($expected, StringCoercionMode::Native->coerce($input));
    }

    public static function nativeCoercionModeProvider(): iterable
    {
        return [
            // Scalars
            ['hello', 'hello'],
            [42, '42'],
            [3.14, '3.14'],
            [1.0, '1'],
            [true, '1'],
            [false, '0'],
            [null, null],

            // Backed enum example
            [TestEnum::StringVal, 'abc'],
            [TestEnum::IntVal, '42'],

            // Stringable
            [new class () implements Stringable {
                public function __toString(): string
                {
                    return 'ok';
                }
            }, 'ok'],

            [new \Uri\Rfc3986\Uri('https://example.com'), 'https://example.com'],
            [new TextDirective('start', 'end'), 'text=start,end'],
            [Query::new(), null],
        ];
    }

    #[DataProvider('ecmaScriptCoercionModeProvider')]
    public function test_it_can_coerce_using_ecmascript_coercion_mode(mixed $input, ?string $expected): void
    {
        self::assertSame($expected, StringCoercionMode::Ecmascript->coerce($input));
    }

    public static function ecmaScriptCoercionModeProvider(): array
    {
        return [
            // Scalars
            ['hello', 'hello'],
            [42, '42'],
            [3.14, '3.14'],
            [1.0, '1.0'],
            [true, 'true'],
            [false, 'false'],
            [null, 'null'],

            // Edge floats
            [NAN, 'NaN'],
            [INF, 'Infinity'],
            [-INF, '-Infinity'],

            // Lists
            [[1, 2, 3], '1,2,3'],
            [[true, null, 3.0], 'true,null,3.0'],
            [[[1, 2], [3]], '1,2,3'],

            // Associative arrays → object
            [['x' => 1, 'y' => 2], '[object Object]'],

            // Objects
            [new stdClass(), '[object Object]'],
            [new class () implements Stringable {
                public function __toString(): string
                {
                    return 'ok';
                }
            }, 'ok'],
            [fn () => 42, '[object Object]'],
            [TestUnitEnum::One, '[object Object]'],

            [new \Uri\WhatWg\Url('https://0:0@0:0/0?0#0'), 'https://0:0@0.0.0.0:0/0?0#0'],
            [new TextDirective('start', 'end'), 'text=start,end'],
            [Query::new(), ''],
        ];
    }

    #[DataProvider('errorProvider')]
    public function testThrowsOnUnsupportedTypes(mixed $input): void
    {
        $this->expectException(TypeError::class);
        StringCoercionMode::Ecmascript->coerce($input);
    }

    public static function errorProvider(): array
    {
        return [
            'resource' => [fopen('php://memory', 'r')],
        ];
    }

    public function test_it_rejects_direct_recursive_array(): void
    {
        $a = [];
        $a[] = &$a;

        $this->expectException(ValueError::class);

        StringCoercionMode::Ecmascript->coerce($a);
    }

    public function test_it_rejects_indirect_recursive_array(): void
    {
        $a = [];
        $b = [];

        $b[] = &$a;
        $a[] = &$b;

        $this->expectException(ValueError::class);

        StringCoercionMode::Ecmascript->coerce($a);
    }

    public function test_it_can_flatten_nested_lists(): void
    {
        $value = [[1, [2]], 3];

        self::assertSame('1,2,3', StringCoercionMode::Ecmascript->coerce($value));
    }

    public function test_it_shared_sub_arrays_are_not_considered_recursive(): void
    {
        $sub = [1, 2];
        $value = [$sub, $sub];

        self::assertSame('1,2,1,2', StringCoercionMode::Ecmascript->coerce($value));
    }
}

enum TestEnum: string
{
    case StringVal = 'abc';
    case IntVal = '42';
}

enum TestUnitEnum
{
    case One;
}
