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

use DOMException;
use JsonSerializable;
use League\Uri\UriString;

/**
 * @phpstan-import-type ComponentMap from UriString
 */
interface UriEncoder extends JsonSerializable
{
    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     */
    public function toString(): string;

    /**
     * Returns the normalized string representation of the URI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-6.2
     */
    public function toNormalizedString(): ?string;

    /**
     * Returns the human-readable string representation of the URI.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-6.2
     */
    public function toDisplayString(): ?string;

    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @see ::__toString
     */
    public function jsonSerialize(): string;

    /**
     * Returns the HTML string representation of the anchor tag with the current instance as its href attribute.
     *
     * @param list<string>|string|null $class
     *
     * @throws DOMException
     */
    public function toAnchorTag(?string $linkText = null, array|string|null $class = null, ?string $target = null): string;

    /**
     * Returns the markdown string representation of the anchor tag with the current instance as its href attribute.
     */
    public function toMarkdown(?string $linkText = null): string;

    /**
     * Returns the Unix filesystem path. The method returns null for any other scheme except the file scheme.
     */
    public function toUnixPath(): ?string;

    /**
     * Returns the Windows filesystem path. The method returns null for any other scheme except the file scheme.
     */
    public function toWindowsPath(): ?string;

    /**
     * Returns an associative array containing all the URI components.
     *
     * @return ComponentMap
     */
    public function toComponents(): array;
}
