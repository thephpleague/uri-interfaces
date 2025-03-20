<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Uri;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;

final class EncoderTest extends TestCase
{
    #[Test]
    #[DataProvider('provideEncodedPath')]
    public function it_can_tell_whether_the_path_is_encoded_or_not(Stringable|string|null $encoded, bool $expected): void
    {
        self::assertSame($expected, Encoder::isPathEncoded($encoded));
    }

    public static function provideEncodedPath(): iterable
    {
        yield 'the path is null' => [
            'encoded' => null,
            'expected' => true,
        ];

        yield 'the path is empty' => [
            'encoded' => '',
            'expected' => true,
        ];

        yield 'the path contains only valid encoded characters' => [
            'encoded' => 'toto%2f%23le$20heros',
            'expected' => true,
        ];

        yield 'the path contains invalid encoded characters' => [
            'encoded' => 'toto%2%23le$20heros',
            'expected' => false,
        ];
    }

    #[Test]
    #[DataProvider('provideEncodedQuery')]
    public function it_can_tell_whether_the_query_is_encoded_or_not(Stringable|string|null $encoded, bool $expected): void
    {
        self::assertSame($expected, Encoder::isQueryEncoded($encoded));
    }

    public static function provideEncodedQuery(): iterable
    {
        yield 'the query is null' => [
            'encoded' => null,
            'expected' => true,
        ];

        yield 'the query is empty' => [
            'encoded' => '',
            'expected' => true,
        ];

        yield 'the query contains only valid encoded characters' => [
            'encoded' => 'toto%2f%23le%20heros=?++',
            'expected' => true,
        ];

        yield 'the query contains invalid encoded characters' => [
            'encoded' => 'toto%2%23le%20heros#',
            'expected' => false,
        ];
    }

    #[Test]
    #[DataProvider('provideEncodedFragment')]
    public function it_can_tell_whether_the_fragment_is_encoded_or_not(Stringable|string|null $encoded, bool $expected): void
    {
        self::assertSame($expected, Encoder::isFragmentEncoded($encoded));
    }

    public static function provideEncodedFragment(): iterable
    {
        yield 'the fragment is null' => [
            'encoded' => null,
            'expected' => true,
        ];

        yield 'the fragment is empty' => [
            'encoded' => '',
            'expected' => true,
        ];

        yield 'the fragment contains only valid encoded characters' => [
            'encoded' => 'toto%2f%23le%20heros=?++',
            'expected' => true,
        ];

        yield 'the query contains invalid encoded characters' => [
            'encoded' => 'toto%2%23le%20herosé',
            'expected' => false,
        ];
    }

    #[Test]
    #[DataProvider('provideEncodedUser')]
    public function it_can_tell_whether_the_user_is_encoded_or_not(Stringable|string|null $encoded, bool $expected): void
    {
        self::assertSame($expected, Encoder::isUserEncoded($encoded));
    }

    public static function provideEncodedUser(): iterable
    {
        yield 'the user is null' => [
            'encoded' => null,
            'expected' => true,
        ];

        yield 'the user is empty' => [
            'encoded' => '',
            'expected' => true,
        ];

        yield 'the user contains only valid encoded characters' => [
            'encoded' => 'toto%2f%23le%20heros',
            'expected' => true,
        ];

        yield 'the query contains invalid encoded characters' => [
            'encoded' => 'toto%2%23le%20heros?@',
            'expected' => false,
        ];
    }

    #[Test]
    #[DataProvider('provideEncodedUser')]
    public function it_can_tell_whether_the_password_is_encoded_or_not(Stringable|string|null $encoded, bool $expected): void
    {
        self::assertSame($expected, Encoder::isPasswordEncoded($encoded));
    }

    public static function provideEncodedPassword(): iterable
    {
        yield 'the password is null' => [
            'encoded' => null,
            'expected' => true,
        ];

        yield 'the password is empty' => [
            'encoded' => '',
            'expected' => true,
        ];

        yield 'the password contains only valid encoded characters' => [
            'encoded' => 'toto%2f%23le%20heros',
            'expected' => true,
        ];

        yield 'the password contains invalid encoded characters' => [
            'encoded' => 'toto%2%23le%20heros?@',
            'expected' => false,
        ];
    }
}
