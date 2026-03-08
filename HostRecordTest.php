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

final class HostRecordTest extends TestCase
{
    #[Test]
    #[DataProvider('provideRecordsForIpTesting')]
    public function it_can_tell_if_a_record_is_an_ip_address(
        ?string $address,
        bool $isIp,
        bool $isRegisteredName,
        bool $isDomain
    ): void {
        self::assertSame($isIp, HostRecord::isIp($address));
        self::assertSame($isRegisteredName, HostRecord::isRegisteredName($address));
        self::assertSame($isDomain, HostRecord::isDomain($address));
    }

    /**
     * @return iterable<non-empty-string, array{
     *     address: ?string,
     *     isIp: bool,
     *     isRegisteredName: bool,
     *     isDomain: bool,
     * }>
     */
    public static function provideRecordsForIpTesting(): iterable
    {
        yield 'a null host is not an IP address' => [
            'address' => null,
            'isIp' => false,
            'isRegisteredName' => true,
            'isDomain' => true,
        ];

        yield 'a registerable host is not an IP address' => [
            'address' => 'example.com',
            'isIp' => false,
            'isRegisteredName' => true,
            'isDomain' => true,
        ];

        yield 'a IPv4 host is an IP address' => [
            'address' => '127.0.0.1',
            'isIp' => true,
            'isRegisteredName' => false,
            'isDomain' => false,
        ];

        yield 'a IPv6 host is an IP address' => [
            'address' => '[::1]',
            'isIp' => true,
            'isRegisteredName' => false,
            'isDomain' => false,
        ];

        yield 'a IPvFuture host is an IP address' => [
            'address' => '[v8.1.2.3]',
            'isIp' => true,
            'isRegisteredName' => false,
            'isDomain' => false,
        ];

        yield 'a random string not registrable is not an IP address' => [
            'address' => '\/?:^',
            'isIp' => false,
            'isRegisteredName' => false,
            'isDomain' => false,
        ];
    }
}
