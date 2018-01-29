<?php
/**
 * League.Uri (http://uri.thephpleague.com)
 *
 * @package    League\Uri
 * @subpackage League\Uri\Interfaces
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @license    https://github.com/thephpleague/uri-interfaces/blob/master/LICENSE (MIT License)
 * @version    1.6.0
 * @link       https://github.com/thephpleague/uri-interfaces/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Uri;

/**
 * Defines constants for common URI encoding type
 *
 * @see https://tools.ietf.org/html/rfc1738
 * @see https://tools.ietf.org/html/rfc3986
 * @see https://tools.ietf.org/html/rfc3987
 *
 * Usage:
 *
 * <code>
 * class Component implements EncodingInterface
 * {
 *     public function getContent(int $enc_type = self::RFC3986_ENCODING)
 *     {
 *     }
 * }
 * </code>
 *
 * @package    League\Uri
 * @subpackage League\Uri
 * @author     Ignace Nyamagana Butera <nyamsprod@gmail.com>
 * @since      1.0.0
 */
interface EncodingInterface
{
    const NO_ENCODING = 0;

    const RFC1738_ENCODING = PHP_QUERY_RFC1738;

    const RFC3986_ENCODING = PHP_QUERY_RFC3986;

    const RFC3987_ENCODING = 3;
}
