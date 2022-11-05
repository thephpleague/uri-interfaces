<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\Contracts;

interface PortInterface extends UriComponentInterface
{
    /**
     * Returns the integer representation of the Port.
     */
    public function toInt(): ?int;
}
