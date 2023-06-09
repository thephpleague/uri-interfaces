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

interface UserInfoInterface extends UriComponentInterface
{
    /**
     * Returns the user component part.
     */
    public function getUsername(): ?string;

    /**
     * Returns the pass component part.
     */
    public function getPassword(): ?string;

    /**
     * Returns an instance with the specified user and/or pass.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified password if the user is specified
     * otherwise it returns the same instance unchanged.
     *
     * An empty user is equivalent to removing the user information.
     */
    public function withPassword(?string $password): self;
}
