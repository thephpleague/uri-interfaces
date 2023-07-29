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

use League\Uri\Idna\IdnaError;
use League\Uri\Idna\IdnaInfo;

final class IdnaConversionFailed extends SyntaxError
{
    private function __construct(string $message, private readonly ?IdnaInfo $idnaInfo = null)
    {
        parent::__construct($message);
    }

    public static function dueToIDNAError(string $domain, IdnaInfo $idnaInfo): self
    {
        $info = array_map(fn (IdnaError $error) => $error->message(), $idnaInfo->errorList());

        return new self(
            'The host `'.$domain.'` is invalid : '.implode(', ', $info).' .',
            $idnaInfo
        );
    }

    public static function dueToInvalidHost(string $domain): self
    {
        return new self('The host `'.$domain.'` is not a valid IDN host');
    }

    public function idnaInfo(): ?IdnaInfo
    {
        return $this->idnaInfo;
    }
}
