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

interface UriInspector
{
    /**
     * Tells whether the URI instance represents an opaque URI.
     */
    public function isOpaque(): bool;

    /**
     * Tells whether the URI represents an absolute URI.
     */
    public function isAbsolute(): bool;

    /**
     * Tells whether the URI represents a network path URI.
     */
    public function isNetworkPath(): bool;

    /**
     * Tells whether the URI represents an absolute URI path.
     */
    public function isAbsolutePath(): bool;

    /**
     * Tells whether the given URI object represents a relative path.
     */
    public function isRelativePath(): bool;

    /**
     * Tells whether the given URI object represents the same document.
     *
     * It never takes the fragment into account
     */
    public function isSameDocument(UriInterface $uri): bool;

    /**
     * Tells whether the given URI object represents the same document.
     *
     * It can take the fragment into account if it is explicitly specified
     */
    public function equals(UriInterface $uri, bool $excludeFragment): bool;

    /**
     * Tells whether the `file` scheme base URI represents a local file.
     */
    public function isLocalFile(): bool;

    /**
     * Tells whether the URI comes from a different origin than the current instance.
     */
    public function isCrossOrigin(UriInterface $uri): bool;

    /**
     * Tells whether the URI shares the same origin as the current instance.
     */
    public function isSameOrigin(UriInterface $uri): bool;

    /**
     * Returns the URI origin as described in the WHATWG URL Living standard specification.
     */
    public function getOrigin(): ?string;
}
