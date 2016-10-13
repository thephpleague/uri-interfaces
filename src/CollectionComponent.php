<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\Interfaces
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @copyright  2016 Ignace Nyamagana Butera
 * @license    https://github.com/thephpleague/uri-interfaces/blob/master/LICENSE (MIT License)
 * @version    1.0.0
 * @link       https://github.com/thephpleague/uri-interfaces/
 */
namespace League\Uri\Interfaces;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;

/**
 * Value object representing a Collection.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * @package    League\Uri
 * @subpackage League\Uri\Interfaces
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
interface CollectionComponent extends Countable, Component, IteratorAggregate
{
    /**
     * Returns the component $keys.
     *
     * Returns the component $keys. If a value is specified
     * only the $keys associated with the given value will be returned
     *
     * @return array
     */
    public function keys();

    /**
     * Returns whether the given key exists in the current instance
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasKey($key);

    /**
     * Returns an instance with filtered elements
     *
     * Iterates over each value in the collection passing them to the callback function.
     * If the callback function returns true, the current value from the collection is returned
     * into the returned new instance.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * @param callable $callable the callback function to use
     * @param int      $flag     flag to determine what argument are sent to callback
     *
     * @throws InvalidArgumentException for transformations that would result in a invalid object.
     *
     * @return static
     */
    public function filter(callable $callable, $flag = 0);

    /**
     * Returns an instance without the specified keys
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the modified component
     *
     * @param array $keys the list of keys to remove from the collection
     *
     * @return static
     */
    public function without(array $keys);
}
