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

namespace League\Uri\Idna;

use League\Uri\Exceptions\SyntaxError;

final class ConversionFailed extends SyntaxError
{
    private function __construct(string $message, private readonly ?Result $result = null)
    {
        parent::__construct($message);
    }

    public static function dueToError(string $domain, Result $result): self
    {
        $info = array_map(fn (Error $error): string => $error->description(), $result->errors());

        return new self(
            'The host `'.$domain.'` is invalid : '.implode(', ', $info).' .',
            $result
        );
    }

    public static function dueToInvalidHost(string $domain): self
    {
        return new self('The host `'.$domain.'` is not a valid IDN host');
    }

    public function result(): ?Result
    {
        return $this->result;
    }
}
