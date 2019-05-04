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
use Iterator;
use IteratorAggregate;

interface QueryInterface extends Countable, IteratorAggregate, UriComponentInterface
{
    /**
     * Returns the query separator.
     */
    public function getSeparator(): string;

    /**
     * Returns the number of key/value pairs present in the object.
     */
    public function count(): int;

    /**
     * Returns an iterator allowing to go through all key/value pairs contained in this object.
     *
     * The pair is represented as an array where the first value is the pair key
     * and the second value the pair value.
     *
     * The key of each pair is a string
     * The value of each pair is a scalar or the null value
     *
     * @return Iterator
     */
    public function getIterator(): iterable;

    /**
     * Returns an iterator allowing to go through all key/value pairs contained in this object.
     *
     * The return type is as a Iterator where its offset is the pair key and its value the pair value.
     *
     * The key of each pair is a string
     * The value of each pair is a scalar or the null value
     *
     * @return Iterator
     */
    public function pairs(): Iterable;

    /**
     * Tell whether a parameter with a specific name exists.
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-has
     */
    public function has(string $key): bool;

    /**
     * Returns the first value associated to the given parameter.
     *
     * If no value is found null is returned
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-get
     */
    public function get(string $key);

    /**
     * Returns all the values associated to the given parameter as an array or all
     * the instance pairs.
     *
     * If no value is found an empty array is returned
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-getall
     */
    public function getAll(string $key): array;

    /**
     * Returns the store PHP variables as elements of an array.
     *
     * The result is similar as PHP parse_str when used with its
     * second argument with the difference that variable names are
     * not mangled.
     *
     * @see http://php.net/parse_str
     * @see https://wiki.php.net/rfc/on_demand_name_mangling
     */
    public function toParams(): array;

    /**
     * Returns the RFC1738 encoded query.
     */
    public function toRFC1738(): ?string;

    /**
     * Returns the RFC3986 encoded query.
     *
     * @see ::getContent
     */
    public function toRFC3986(): ?string;

    /**
     * Returns an instance with a different separator.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component with a different separator
     *
     * @return static
     */
    public function withSeparator(string $separator);

    /**
     * Sort the query string by offset, maintaining offset to data correlations.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-sort
     *
     * @return static
     */
    public function sort();

    /**
     * Returns an instance without duplicate key/value pair.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized by removing
     * duplicate pairs whose key/value are the same.
     *
     * @return static
     */
    public function withoutDuplicates();

    /**
     * Returns an instance without empty key/value where the value is the null value.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized by removing
     * empty pairs.
     *
     * A pair is considered empty if its value is equal to the null value
     *
     * @return static
     */
    public function withoutEmptyPairs();

    /**
     * Returns an instance where numeric indices associated to PHP's array like key are removed.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the query component normalized so that numeric indexes
     * are removed from the pair key value.
     *
     * ie.: toto[3]=bar[3]&foo=bar becomes toto[]=bar[3]&foo=bar
     *
     * @return static
     */
    public function withoutNumericIndices();

    /**
     * Returns an instance with the a new key/value pair added to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * If the pair already exists the value will replace the existing value.
     *
     * @see https://url.spec.whatwg.org/#dom-urlsearchparams-set
     *
     * @return static
     */
    public function withPair(string $key, $value);

    /**
     * Returns an instance with the new pairs set to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * @see ::withPair
     *
     * @return static
     */
    public function merge($query);

    /**
     * Returns an instance without the specified keys.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * @param string $key     the first key to remove
     * @param string ...$keys the list of remaining keys to remove
     *
     * @return static
     */
    public function withoutPair(string $key, string ...$keys);

    /**
     * Returns a new instance with a specified key/value pair appended as a new pair.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * @param mixed $value must be a scalar or the null value
     *
     * @return static
     */
    public function appendTo(string $key, $value);

    /**
     * Returns an instance with the new pairs appended to it.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified query
     *
     * If the pair already exists the value will be added to it.
     *
     * @return static
     */
    public function append($query);

    /**
     * Returns an instance without the specified params.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component without PHP's value.
     * PHP's mangled is not taken into account.
     *
     * @param string ...$offsets
     *
     * @return static
     */
    public function withoutParam(string $offset, string ...$offsets);
}
