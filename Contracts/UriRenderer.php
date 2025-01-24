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
use RuntimeException;
use SplFileInfo;
use SplFileObject;
use Stringable;

/**
 * @phpstan-import-type ComponentMap from UriString
 */
interface UriRenderer extends JsonSerializable
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
    public function toDisplayString(): string;

    /**
     * Returns the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @see ::__toString
     */
    public function jsonSerialize(): string;

    /**
     * Returns the markdown string representation of the anchor tag with the current instance as its href attribute.
     */
    public function toMarkdownAnchor(?string $linkTextTemplate = null): string;

    /**
     * Returns the HTML string representation of the anchor tag with the current instance as its href attribute.
     *
     * @param iterable<string, string|null> $attributes an ordered map of key value. you must quote the value if needed
     *
     * @throws DOMException
     */
    public function toHtmlAnchor(?string $linkTextTemplate = null, iterable $attributes = []): string;

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

    /**
     * Returns a string representation of a File URI according to RFC8089.
     *
     * The method will return null if the URI scheme is not the `file` scheme
     *
     * @see https://datatracker.ietf.org/doc/html/rfc8089
     */
    public function toRfc8089(): ?string;

    /**
     * Save the data to a specific file.
     *
     * The method returns the number of bytes written to the file
     * or null for any other scheme except the data scheme
     *
     * @param SplFileInfo|SplFileObject|resource|Stringable|string $destination
     * @param ?resource $context
     *
     * @throws RuntimeException if the content can not be stored.
     */
    public function toFileContents(mixed $destination, $context = null): ?int;
}
