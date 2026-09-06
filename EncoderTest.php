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

    /**
     * RFC 3986 §2.3 unreserved = ALPHA / DIGIT / "-" / "." / "_" / "~".
     */
    private static function isUnreserved(int $byte): bool
    {
        return ($byte >= 0x41 && $byte <= 0x5A)          // A-Z
            || ($byte >= 0x61 && $byte <= 0x7A)          // a-z
            || ($byte >= 0x30 && $byte <= 0x39)          // 0-9
            || in_array($byte, [0x2D, 0x2E, 0x5F, 0x7E], true); // - . _ ~
    }

    #[Test]
    public function it_decodes_every_unreserved_triplet_and_leaves_the_rest_encoded(): void
    {
        $underDecoded = [];
        $overDecoded = [];
        for ($byte = 0x00; $byte <= 0xFF; $byte++) {
            $triplet = sprintf('%%%02X', $byte);
            $isDecoded = Encoder::decodeUnreservedCharacters($triplet) !== $triplet;
            match (true) {
                self::isUnreserved($byte) && !$isDecoded => $underDecoded[] = $triplet,
                !self::isUnreserved($byte) && $isDecoded => $overDecoded[] = $triplet,
                default => null,
            };
        }

        self::assertSame([], $underDecoded, 'unreserved characters were left percent-encoded');
        self::assertSame([], $overDecoded, 'non unreserved characters were wrongly decoded');
    }

    #[Test]
    #[DataProvider('provideUnreservedUserInfo')]
    public function it_decodes_the_unreserved_set_in_user_and_password(string $encoded, string $expected): void
    {
        self::assertSame($expected, Encoder::normalizeUser($encoded));
        self::assertSame($expected, Encoder::normalizePassword($encoded));
    }

    public static function provideUnreservedUserInfo(): iterable
    {
        yield 'uppercase letters P to Y were previously left encoded' => [
            'encoded' => '%50%51%52%53%54%55%56%57%58%59',
            'expected' => 'PQRSTUVWXY',
        ];

        yield 'boundary letters O, P and Z all decode' => [
            'encoded' => '%4F%50%5A',
            'expected' => 'OPZ',
        ];

        yield 'lowercase hex digits decode to the same character' => [
            'encoded' => '%5a%59',
            'expected' => 'ZY',
        ];
    }

    #[Test]
    #[DataProvider('provideNonUnreserved')]
    public function it_keeps_non_unreserved_characters_percent_encoded(string $encoded): void
    {
        self::assertSame($encoded, Encoder::decodeUnreservedCharacters($encoded));
    }

    public static function provideNonUnreserved(): iterable
    {
        yield 'curly braces and vertical bar were previously over-decoded' => ['encoded' => '%7B%7C%7D'];
        yield 'brackets, backslash and caret stay encoded' => ['encoded' => '%5B%5C%5D%5E'];
        yield 'grave accent stays encoded' => ['encoded' => '%60'];
    }

    #[Test]
    public function it_normalizes_equivalent_userinfo_encodings_to_the_same_uri(): void
    {
        $normalized = (string) Uri::new('https://%50%51@ex.com/')->normalize();

        self::assertSame('https://PQ@ex.com/', $normalized);
        self::assertSame((string) Uri::new('https://PQ@ex.com/')->normalize(), $normalized);
    }

    #[Test]
    public function it_decodes_the_unreserved_set_idempotently(): void
    {
        $once = Encoder::decodeUnreservedCharacters('%50%51%7B%7E');

        self::assertSame('PQ%7B~', $once);
        self::assertSame($once, Encoder::decodeUnreservedCharacters($once));
    }
}
