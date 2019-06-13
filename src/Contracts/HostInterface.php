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

namespace League\Uri\Contracts;

interface HostInterface extends UriComponentInterface
{
    /**
     * Returns the ascii representation.
     */
    public function toAscii(): ?string;

    /**
     * Returns the unicode representation.
     */
    public function toUnicode(): ?string;

    /**
     * Returns the IP version.
     *
     * If the host is a not an IP this method will return null
     */
    public function getIpVersion(): ?string;

    /**
     * Returns the IP component If the Host is an IP adress.
     *
     * If the host is a not an IP this method will return null
     */
    public function getIp(): ?string;

    /**
     * Tells whether the host is a domain name.
     */
    public function isDomain(): bool;

    /**
     * Tells whether the host is an IP Address.
     */
    public function isIp(): bool;

    /**
     * Returns whether or not the host is an IPv4 address.
     */
    public function isIpv4(): bool;

    /**
     * Returns whether or not the host is an IPv6 address.
     */
    public function isIpv6(): bool;

    /**
     * Returns whether or not the host is an IPv6 address.
     */
    public function isIpFuture(): bool;

    /**
     * Returns whether or not the host has a ZoneIdentifier.
     *
     * @see http://tools.ietf.org/html/rfc6874#section-4
     */
    public function hasZoneIdentifier(): bool;

    /**
     * Returns an host without its zone identifier according to RFC6874.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance without the host zone identifier according to RFC6874
     *
     * @see http://tools.ietf.org/html/rfc6874#section-4
     *
     * @return static
     */
    public function withoutZoneIdentifier(): self;
}
