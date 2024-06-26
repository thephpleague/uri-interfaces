<?php

declare(strict_types=1);

namespace League\Uri\IPv6;

use Stringable;
use ValueError;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;

use function filter_var;
use function inet_pton;
use function implode;
use function str_split;
use function strtolower;
use function unpack;

final class Converter
{
    public static function compressIp(string $ipv6): string
    {
        if (false === filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new ValueError('The submitted IP is not a valid IPv6 address.');
        }

        return (string) inet_ntop((string) inet_pton($ipv6));
    }

    public static function expandIp(string $ipv6): string
    {
        if (false === filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new ValueError('The submitted IP is not a valid IPv6 address.');
        }

        $hex = (array) unpack("H*hex", (string) inet_pton($ipv6));

        return implode(':', str_split(strtolower($hex['hex'] ?? ''), 4));
    }

    public static function compress(Stringable|string|null $host): ?string
    {
        $components = self::parse($host);
        if (null === $components['ipv6']) {
            return match ($host) {
                null => $host,
                default => (string) $host,
            };
        }

        $components['ipv6'] = self::compressIp($components['ipv6']);

        return self::build($components);
    }

    public static function expand(Stringable|string|null $host): ?string
    {
        $components = self::parse($host);
        if (null === $components['ipv6']) {
            return match ($host) {
                null => $host,
                default => (string) $host,
            };
        }

        $components['ipv6'] = self::expandIp($components['ipv6']);

        return self::build($components);
    }

    private static function build(array $components): string
    {
        $components['ipv6'] ??= null;
        $components['zoneIdentifier'] ??= null;

        return '['.$components['ipv6'].match ($components['zoneIdentifier']) {
            null => '',
            default => '%'.$components['zoneIdentifier'],
        }.']';
    }

    /**]
     * @param Stringable|string|null $host
     *
     * @return array{ipv6:?string, zoneIdentifier:?string}
     */
    private static function parse(Stringable|string|null $host): array
    {
        if ($host === null) {
            return ['ipv6' => null, 'zoneIdentifier' => null];
        }

        $host = (string) $host;
        if ($host === '') {
            return ['ipv6' => null, 'zoneIdentifier' => null];
        }

        if (!str_starts_with($host, '[')) {
            return ['ipv6' => null, 'zoneIdentifier' => null];
        }

        if (!str_ends_with($host, ']')) {
            return ['ipv6' => null, 'zoneIdentifier' => null];
        }

        [$ipv6, $zoneIdentifier] = explode('%', substr($host, 1, -1), 2) + [1 => null];
        if (false === filter_var($ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return ['ipv6' => null, 'zoneIdentifier' => null];
        }

        return ['ipv6' => $ipv6, 'zoneIdentifier' => $zoneIdentifier];
    }
}
