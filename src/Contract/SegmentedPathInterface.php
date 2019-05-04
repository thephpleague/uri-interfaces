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

namespace League\Uri\Contract;

use Countable;
use IteratorAggregate;
use League\Uri\Exception\OffsetOutOfBounds;

interface SegmentedPathInterface extends Countable, IteratorAggregate, PathInterface
{
    public const IS_ABSOLUTE = 1;

    public const IS_RELATIVE = 0;

    public const SEPARATOR = '/';

    /**
     * Returns the total number of segments in the path.
     */
    public function count(): int;

    /**
     * Iterate over the path segment.
     */
    public function getIterator(): iterable;

    /**
     * Returns parent directory's path.
     */
    public function getDirname(): string;

    /**
     * Returns the path basename.
     */
    public function getBasename(): string;

    /**
     * Returns the basename extension.
     */
    public function getExtension(): string;

    /**
     * Retrieves a single path segment.
     *
     * Retrieves a single path segment.
     *
     * If the segment offset has not been set, returns null.
     */
    public function get(int $offset): ?string;

    /**
     * Returns the associated key for a specific segment.
     *
     * If a value is specified only the keys associated with
     * the given value will be returned
     */
    public function keys(string $segment): array;

    /**
     * Appends a segment to the path.
     *
     * @return static
     */
    public function append($segment);

    /**
     * Prepends a segment to the path.
     *
     * @return static
     */
    public function prepend($segment);

    /**
     * Returns an instance with the modified segment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the new segment
     *
     * If $key is non-negative, the added segment will be the segment at $key position from the start.
     * If $key is negative, the added segment will be the segment at $key position from the end.
     *
     * @throws OffsetOutOfBounds If the key is invalid
     *
     * @return static
     */
    public function withSegment(int $key, $segment);

    /**
     * Returns an instance without the specified segment.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * If $key is non-negative, the removed segment will be the segment at $key position from the start.
     * If $key is negative, the removed segment will be the segment at $key position from the end.
     *
     * @param int $key     required key to remove
     * @param int ...$keys remaining keys to remove
     *
     * @throws OffsetOutOfBounds If the key is invalid
     *
     * @return static
     */
    public function withoutSegment(int $key, int ...$keys);

    /**
     * Returns an instance without duplicate delimiters.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component normalized by removing
     * multiple consecutive empty segment
     *
     * @return static
     */
    public function withoutEmptySegments();

    /**
     * Returns an instance with the specified parent directory's path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     *
     * @return static
     */
    public function withDirname($path);

    /**
     * Returns an instance with the specified basename.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     *
     * @return static
     */
    public function withBasename($basename);

    /**
     * Returns an instance with the specified basename extension.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the extension basename modified.
     *
     * @return static
     */
    public function withExtension($extension);
}
