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

use InvalidArgumentException;

/**
 * Value object representing a URI Path component.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * @see        https://tools.ietf.org/html/rfc3986#section-3.3
 * @package    League\Uri
 * @subpackage League\Uri\Interfaces
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
interface PathComponent extends Component
{
    /**
     * Returns whether or not the path is absolute or relative
     *
     * @return bool
     */
    public function isAbsolute();

    /**
     * Returns an instance with a leading slash
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component with a leading slash
     *
     * @throws InvalidArgumentException for transformations that would result in a invalid object.
     *
     * @return static
     */
    public function withLeadingSlash();

    /**
     * Returns an instance without a leading slash
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component without a leading slash
     *
     * @throws InvalidArgumentException for transformations that would result in a invalid object.
     *
     * @return static
     */
    public function withoutLeadingSlash();

    /**
     * Returns an instance without dot segments
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the path component normalized by removing
     * the dot segment.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-5.2.4
     *
     * @return static
     */
    public function withoutDotSegments();
}
