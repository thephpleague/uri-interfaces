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
 * Value object representing a URI component.
 *
 * Instances of this interface are considered immutable; all methods that
 * might change state MUST be implemented such that they retain the internal
 * state of the current instance and return an instance that contains the
 * changed state.
 *
 * @see        https://tools.ietf.org/html/rfc3986#section-3
 * @package    League\Uri
 * @subpackage League\Uri\Interfaces
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
interface Component
{
    const RFC3986_ENCODING = 2;

    const RFC3987_ENCODING = 3;

    const NO_ENCODING = 255;

    /**
     * Returns whether or not the component is defined.
     *
     * @return bool
     */
    public function isDefined();

    /**
     * Returns the instance content.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters depending on the encoding type selected.
     *
     * To determine what characters to encode, please refer to RFC 3986, Sections 2 and 3.
     * or RFC 3987 Section 3. By default the content is encoded according to RFC3986
     *
     * If the instance is not defined null is returned
     *
     * @param int $enc_type
     *
     * @return string|null
     */
    public function getContent($enc_type = self::RFC3986_ENCODING);

    /**
     * Returns the instance string representation.
     *
     * If the instance is defined, the value returned MUST be percent-encoded,
     * but MUST NOT double-encode any characters. To determine what characters
     * to encode, please refer to RFC 3986, Sections 2 and 3.
     *
     * If the instance is not defined an empty string is returned
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns the instance string representation with its optional URI delimiters
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode any
     * characters. To determine what characters to encode, please refer to RFC 3986,
     * Sections 2 and 3.
     *
     * If the instance is not defined an empty string is returned
     *
     * @return string
     */
    public function getUriComponent();

    /**
     * Returns an instance with the specified content.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified content.
     *
     * Users can provide both encoded and decoded content characters.
     *
     * A null value is equivalent to removing the component content.
     *
     * @param string|null $content
     *
     * @throws InvalidArgumentException for invalid component or transformations
     *                                  that would result in a object in invalid state.
     *
     * @return static
     */
    public function withContent($content);
}
