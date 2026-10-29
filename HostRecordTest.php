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

    #[Test]
    #[DataProvider('provideRecordsForRegisteredNameTesting')]
    public function it_can_parse_host_issue_230(
        string $host,
        bool $isIp,
        bool $isRegisteredName,
        bool $isDomain
    ): void {
        self::assertSame($isIp, HostRecord::isIp($host));
        self::assertSame($isRegisteredName, HostRecord::isRegisteredName($host));
        self::assertSame($isDomain, HostRecord::isDomain($host));
    }

    public static function provideRecordsForRegisteredNameTesting(): iterable
    {
        yield 'a host containing an encoded reserved character (1)' => [
            'host' => 'exa%3Cmple.com',
            'isIp' => false,
            'isRegisteredName' => true,
            'isDomain' => false,
        ];

        yield 'a host containing encoded reserved character (2)' => [
            'host' => 'example.com%2F.example.org',
            'isIp' => false,
            'isRegisteredName' => true,
            'isDomain' => false,
        ];

        yield 'a host containing an encoded invalid unicoded character' => [
            'host' => 'a'.rawurlencode('⒈').'.com',
            'isIp' => false,
            'isRegisteredName' => true,
            'isDomain' => false,
        ];
    }
}
