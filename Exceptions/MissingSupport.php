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

namespace League\Uri\Exceptions;

use League\Uri\Contracts\UriException;
use League\Uri\IPv4\Calculator;
use RuntimeException;

class MissingSupport extends RuntimeException implements UriException
{
    public static function forIDN(): self
    {
        return new self('Support for IDN host requires the `intl` extension for best performance or run "composer require symfony/polyfill-intl-idn".');
    }

    public static function forMathCalculator(): self
    {
        return new self('A '.Calculator::class.' could not be automatically found. To perform IPv4 conversion use a x.64 PHP build or install one of the following extension GMP or BCMath or ship your own implmentation.');
    }

    public static function forFileInfo(): self
    {
        return new self('Please install ext/fileinfo to perform file type detection.');
    }
}
