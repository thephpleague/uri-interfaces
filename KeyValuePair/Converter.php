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

namespace League\Uri\KeyValuePair;

use League\Uri\Contracts\UriComponentInterface;
use League\Uri\Exceptions\SyntaxError;
use Stringable;
use function explode;
use function implode;
use function preg_match;
use function str_replace;
use const PHP_QUERY_RFC1738;
use const PHP_QUERY_RFC3986;

final class Converter
{
    private const REGEXP_INVALID_CHARS = '/[\x00-\x1f\x7f]/';

    /**
     * @param non-empty-string $separator
     * @param array<string>    $toRFC3986Encoding
     * @param array<string>    $toEncoding
     */
    private function __construct(
        private readonly string $separator,
        private readonly array $toRFC3986Encoding = [],
        private readonly array $toEncoding = [],
    ) {
    }

    /**
     * @param non-empty-string $separator
     */
    public static function new(string $separator): self
    {
        return new self($separator);
    }

    /**
     * @param non-empty-string $separator
     */
    public static function fromRFC3986(string $separator = '&'): self
    {
        return self::new($separator);
    }

    /**
     * @param non-empty-string $separator
     */
    public static function fromRFC1738(string $separator = '&'): self
    {
        return self::new($separator)
            ->withRfc3986Output('%20')
            ->withEncodingOutput('+');
    }

    public static function fromEncodingType(int $encType): self
    {
        return match (true) {
            PHP_QUERY_RFC3986 === $encType => self::fromRFC3986(),
            PHP_QUERY_RFC1738 === $encType => self::fromRFC1738(),
            default => throw new SyntaxError('Unknown or Unsupported encoding.'),
        };
    }

    /**
     * @return array<non-empty-list<string|null>>
     */
    public function toPairs(Stringable|string|bool|null $value): array
    {
        $filteredValue = $this->filterValue($value);

        return array_map(
            fn (string $pair): array => explode('=', $pair, 2) + [1 => null],
            match (true) {
                null === $filteredValue => [],
                default => explode($this->separator, $filteredValue),
            }
        );
    }

    /**
     * @param iterable<array{0:string|null, 1:string|null}> $pairs
     */
    public function toValue(iterable $pairs): ?string
    {
        $filteredPairs = [];
        foreach ($pairs as $pair) {
            $filteredPairs[] = match (true) {
                !is_string($pair[0]) => throw new SyntaxError('the pair key MUST be a string;, `'.gettype($pair[0]).'` given.'),
                null === $pair[1] => $pair[0],
                default => $pair[0].'='.$pair[1],
            };
        }

        return match (true) {
            [] === $filteredPairs => null,
            default => str_replace($this->toRFC3986Encoding, $this->toEncoding, implode($this->separator, $filteredPairs)),
        };
    }

    /**
     * @param non-empty-string $separator
     */
    public function withSeparator(string $separator): self
    {
        return match (true) {
            '' === $separator => throw new SyntaxError('The separator character can not be the empty string.'), /* @phpstan-ignore-line */
            $separator === $this->separator => $this,
            default => new self($separator, $this->toRFC3986Encoding, $this->toEncoding),
        };
    }

    public function withRfc3986Output(string ...$encoding): self
    {
        return match (true) {
            $encoding === $this->toRFC3986Encoding => $this,
            default => new self($this->separator, $encoding, $this->toEncoding),
        };
    }

    public function withEncodingOutput(string ...$encoding): self
    {
        return match (true) {
            $encoding === $this->toEncoding => $this,
            default => new self($this->separator, $this->toRFC3986Encoding, $encoding),
        };
    }

    private function filterValue(Stringable|string|bool|null $query): ?string
    {
        $query = match (true) {
            $query instanceof UriComponentInterface => $query->value(),
            $query instanceof Stringable => (string) $query,
            default => $query,
        };

        return match (true) {
            null === $query => null,
            false === $query => '0',
            true === $query => '1',
            1 === preg_match(self::REGEXP_INVALID_CHARS, $query) => throw new SyntaxError('Invalid query string: `'.$query.'`.'),
            default => str_replace($this->toEncoding, $this->toRFC3986Encoding, $query),
        };
    }
}
