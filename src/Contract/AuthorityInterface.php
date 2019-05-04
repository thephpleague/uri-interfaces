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

use League\Uri\Exception\IdnSupportMissing;
use League\Uri\Exception\SyntaxError;

interface AuthorityInterface extends UriComponentInterface
{
    /**
     * Returns the host component of the authority.
     */
    public function getHost(): ?string;

    /**
     * Returns the port component of the authority.
     */
    public function getPort(): ?int;

    /**
     * Returns the user information component of the authority.
     */
    public function getUserInfo(): ?string;

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * A null value provided for the host is equivalent to removing the host
     * information.
     *
     * @param mixed|null $host
     *
     * @throws SyntaxError       for invalid component or transformations
     *                           that would result in a object in invalid state.
     * @throws IdnSupportMissing for component or transformations
     *                           requiring IDN support when IDN support is not present
     *                           or misconfigured.
     * @return static
     */
    public function withHost($host);

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param mixed|null $port
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     * @return static
     */
    public function withPort($port);

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; a null value for the user is equivalent to removing user
     * information.
     *
     * @param ?string $user
     * @param ?string $password
     *
     * @throws SyntaxError for invalid component or transformations
     *                     that would result in a object in invalid state.
     * @return static
     */
    public function withUserInfo($user, $password = null);
}
