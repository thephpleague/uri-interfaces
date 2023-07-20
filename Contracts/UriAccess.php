<?php

declare(strict_types=1);

namespace League\Uri\Contracts;

use Psr\Http\Message\UriInterface as Psr7UriInterface;

interface UriAccess
{
    public function getUri(): UriInterface|Psr7UriInterface;
}
