<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Uri;

use League\Uri\Contracts\UriException;
use League\Uri\Exceptions\SyntaxError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;

use function rawurlencode;

#[CoversClass(UriString::class)]
final class UriStringTest extends TestCase
{
    private const BASE_URI = 'http://a/b/c/d;p?q';

    #[DataProvider('validUriProvider')]
    public function testParseSucced(Stringable|string|int $uri, array $expected): void
    {
        self::assertSame($expected, UriString::parse($uri));
    }

    public static function validUriProvider(): array
    {
        return [
            'scheme with non-leading digit' => [
                's3://somebucket/somefile.txt',
                [
                    'scheme' => 's3',
                    'user' => null,
                    'pass' => null,
                    'host' => 'somebucket',
                    'port' => null,
                    'path' => '/somefile.txt',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'uri with host ascii version' => [
                'scheme://user:pass@xn--mgbh0fb.xn--kgbechtv',
                [
                    'scheme' => 'scheme',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'xn--mgbh0fb.xn--kgbechtv',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'complete URI' => [
                'scheme://user:pass@host:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'host',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI is not normalized' => [
                'ScheMe://user:pass@HoSt:81/path?query#fragment',
                [
                    'scheme' => 'ScheMe',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'HoSt',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without scheme' => [
                '//user:pass@HoSt:81/path?query#fragment',
                [
                    'scheme' => null,
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'HoSt',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without empty authority only' => [
                '//',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => '',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI without userinfo' => [
                'scheme://HoSt:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => 'HoSt',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI with empty userinfo' => [
                'scheme://@HoSt:81/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => '',
                    'pass' => null,
                    'host' => 'HoSt',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without port' => [
                'scheme://user:pass@host/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'host',
                    'port' => null,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI with an empty port' => [
                'scheme://user:pass@host:/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'host',
                    'port' => null,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without user info and port' => [
                'scheme://host/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => 'host',
                    'port' => null,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI with host IP' => [
                'scheme://10.0.0.2/p?q#f',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => '10.0.0.2',
                    'port' => null,
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
            ],
            'URI with scoped IP' => [
                'scheme://[fe80:1234::%251]/p?q#f',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => '[fe80:1234::%251]',
                    'port' => null,
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
            ],
            'URI with IP future' => [
                'scheme://[vAF.1::2::3]/p?q#f',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => '[vAF.1::2::3]',
                    'port' => null,
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
            ],
            'URI without authority' => [
                'scheme:path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without authority and scheme' => [
                '/path',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '/path',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI with empty host' => [
                'scheme:///path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => '',
                    'port' => null,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI with empty host and without scheme' => [
                '///path?query#fragment',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => '',
                    'port' => null,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without path' => [
                'scheme://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => null,
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without path and scheme' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => null,
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'URI without scheme with IPv6 host and port' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?query#fragment',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => 42,
                    'path' => '',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
            'complete URI without scheme' => [
                '//user@[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?q#f',
                [
                    'scheme' => null,
                    'user' => 'user',
                    'pass' => null,
                    'host' => '[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]',
                    'port' => 42,
                    'path' => '',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
            ],
            'URI without authority and query' => [
                'scheme:path#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'path',
                    'query' => null,
                    'fragment' => 'fragment',
                ],
            ],
            'URI with empty query' => [
                'scheme:path?#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'path',
                    'query' => '',
                    'fragment' => 'fragment',
                ],
            ],
            'URI with query only' => [
                '?query',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => 'query',
                    'fragment' => null,
                ],
            ],
            'URI without fragment' => [
                'tel:05000',
                [
                    'scheme' => 'tel',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '05000',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI with empty fragment' => [
                'scheme:path#',
                [
                    'scheme' => 'scheme',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'path',
                    'query' => null,
                    'fragment' => '',
                ],
            ],
            'URI with fragment only' => [
                '#fragment',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => 'fragment',
                ],
            ],
            'URI with empty fragment only' => [
                '#',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => '',
                ],
            ],
            'URI without authority 2' => [
                'path#fragment',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'path',
                    'query' => null,
                    'fragment' => 'fragment',
                ],
            ],
            'URI with empty query and fragment' => [
                '?#',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => '',
                    'fragment' => '',
                ],
            ],
            'URI with absolute path' => [
                '/?#',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '/',
                    'query' => '',
                    'fragment' => '',
                ],
            ],
            'URI with absolute authority' => [
                'https://thephpleague.com./p?#f',
                [
                    'scheme' => 'https',
                    'user' => null,
                    'pass' => null,
                    'host' => 'thephpleague.com.',
                    'port' => null,
                    'path' => '/p',
                    'query' => '',
                    'fragment' => 'f',
                ],
            ],
            'URI with absolute path only' => [
                '/',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '/',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI with empty query only' => [
                '?',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => '',
                    'fragment' => null,
                ],
            ],
            'relative path' => [
                '../relative/path',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '../relative/path',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'complex authority' => [
                'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                [
                    'scheme' => 'http',
                    'user' => 'a_.!~*\'(-)n0123Di%25%26',
                    'pass' => 'pass;:&=+$,word',
                    'host' => 'www.zend.com',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'complex authority without scheme' => [
                '//a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                [
                    'scheme' => null,
                    'user' => 'a_.!~*\'(-)n0123Di%25%26',
                    'pass' => 'pass;:&=+$,word',
                    'host' => 'www.zend.com',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'single word is a path' => [
                'http',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'http',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI scheme with an empty authority' => [
                'http://',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => '',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'single word is a path, no' => [
                'http:::/path',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '::/path',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'fragment with pseudo segment' => [
                'http://example.com#foo=1/bar=2',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => 'example.com',
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => 'foo=1/bar=2',
                ],
            ],
            'complex URI' => [
                'htà+d/s:totot',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => 'htà+d/s:totot',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'scheme only URI' => [
                'http:',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'RFC3986 LDAP example' => [
                'ldap://[2001:db8::7]/c=GB?objectClass?one',
                [
                    'scheme' => 'ldap',
                    'user' => null,
                    'pass' => null,
                    'host' => '[2001:db8::7]',
                    'port' => null,
                    'path' => '/c=GB',
                    'query' => 'objectClass?one',
                    'fragment' => null,
                ],
            ],
            'RFC3987 example' => [
                'http://bébé.bé./有词法别名.zh',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => 'bébé.bé.',
                    'port' => null,
                    'path' => '/有词法别名.zh',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'colon detection respect RFC3986 (1)' => [
                'http://example.org/hello:12?foo=bar#test',
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => 'example.org',
                    'port' => null,
                    'path' => '/hello:12',
                    'query' => 'foo=bar',
                    'fragment' => 'test',
                ],
            ],
            'colon detection respect RFC3986 (2)' => [
                '/path/to/colon:34',
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '/path/to/colon:34',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'scheme with hyphen' => [
                'android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
                [
                    'scheme' => 'android-app',
                    'user' => null,
                    'pass' => null,
                    'host' => 'org.wikipedia',
                    'port' => null,
                    'path' => '/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI is a scalar value' => [
                1234,
                [
                    'scheme' => null,
                    'user' => null,
                    'pass' => null,
                    'host' => null,
                    'port' => null,
                    'path' => '1234',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            'URI is a object with __toString' => [
                new class () {
                    public function __toString(): string
                    {
                        return 'http://example.org/hello:12?foo=bar#test';
                    }
                },
                [
                    'scheme' => 'http',
                    'user' => null,
                    'pass' => null,
                    'host' => 'example.org',
                    'port' => null,
                    'path' => '/hello:12',
                    'query' => 'foo=bar',
                    'fragment' => 'test',
                ],
            ],
            'Authority is the colon' => [
                'ftp://:/p?q#f',
                [
                    'scheme' => 'ftp',
                    'user' => null,
                    'pass' => null,
                    'host' => '',
                    'port' => null,
                    'path' => '/p',
                    'query' => 'q',
                    'fragment' => 'f',
                ],
            ],
            'URI with 0 leading port' => [
                'scheme://user:pass@host:000000000081/path?query#fragment',
                [
                    'scheme' => 'scheme',
                    'user' => 'user',
                    'pass' => 'pass',
                    'host' => 'host',
                    'port' => 81,
                    'path' => '/path',
                    'query' => 'query',
                    'fragment' => 'fragment',
                ],
            ],
        ];
    }

    #[DataProvider('invalidUriProvider')]
    public function testParseFailed(string $uri): void
    {
        self::expectException(SyntaxError::class);
        UriString::parse($uri);
    }

    public static function invalidUriProvider(): array
    {
        return [
            'invalid scheme' => ['0scheme://host/path?query#fragment'],
            'invalid path' => ['://host:80/p?q#f'],
            'invalid port (1)' => ['//host:port/path?query#fragment'],
            'invalid port (2)' => ['//host:-892358/path?query#fragment'],
            'invalid host' => ['http://exam ple.com'],
            'invalid ipv6 host (1)' => ['scheme://[127.0.0.1]/path?query#fragment'],
            'invalid ipv6 host (2)' => ['scheme://]::1[/path?query#fragment'],
            'invalid ipv6 host (3)' => ['scheme://[::1|/path?query#fragment'],
            'invalid ipv6 host (4)' => ['scheme://|::1]/path?query#fragment'],
            'invalid ipv6 host (5)' => ['scheme://[::1]./path?query#fragment'],
            'invalid ipv6 host (6)' => ['scheme://[[::1]]:80/path?query#fragment'],
            'invalid ipv6 scoped (1)' => ['scheme://[::1%25%23]/path?query#fragment'],
            'invalid ipv6 scoped (2)' => ['scheme://[fe80::1234::%251]/path?query#fragment'],
            'invalid char on URI' => ["scheme://host/path/\r\n/toto"],
            'invalid path only URI' => ['2620:0:1cfe:face:b00c::3'],
            'invalid path PHP bug #72811' => ['[::1]:80'],
            'invalid ipvfuture' => ['//[v6.::1]/p?q#f'],
            'invalid RFC3987 host' => ['//a⒈com/p?q#f'],
            'invalid RFC3987 host URL encoded' => ['//'.rawurlencode('a⒈com').'/p?q#f'],
            'invalid Host with fullwith (1)' =>  ['http://％００.com'],
            'invalid host with fullwidth escaped' =>  ['http://%ef%bc%85%ef%bc%94%ef%bc%91.com],'],
            //'invalid pseudo IDN to ASCII string' => ['http://xn--3/foo.'],
            'invalid IDN' => ['//:�@�����������������������������������������������������������������������������������������/'],
        ];
    }

    #[DataProvider('buildUriProvider')]
    public function testBuild(string $uri, string $expected): void
    {
        self::assertSame($expected, UriString::build(UriString::parse($uri)));
    }

    public static function buildUriProvider(): array
    {
        return [
            'complete URI' => [
                'scheme://user:pass@host:81/path?query#fragment',
                'scheme://user:pass@host:81/path?query#fragment',
            ],
            'URI is not normalized' => [
                'ScheMe://user:pass@HoSt:81/path?query#fragment',
                'ScheMe://user:pass@HoSt:81/path?query#fragment',
            ],
            'URI without scheme' => [
                '//user:pass@HoSt:81/path?query#fragment',
                '//user:pass@HoSt:81/path?query#fragment',
            ],
            'URI without empty authority only' => [
                '//',
                '//',
            ],
            'URI without userinfo' => [
                'scheme://HoSt:81/path?query#fragment',
                'scheme://HoSt:81/path?query#fragment',
            ],
            'URI with empty userinfo' => [
                'scheme://@HoSt:81/path?query#fragment',
                'scheme://@HoSt:81/path?query#fragment',
            ],
            'URI without port' => [
                'scheme://user:pass@host/path?query#fragment',
                'scheme://user:pass@host/path?query#fragment',
            ],
            'URI with an empty port' => [
                'scheme://user:pass@host:/path?query#fragment',
                'scheme://user:pass@host/path?query#fragment',
            ],
            'URI without user info and port' => [
                'scheme://host/path?query#fragment',
                'scheme://host/path?query#fragment',
            ],
            'URI with host IP' => [
                'scheme://10.0.0.2/p?q#f',
                'scheme://10.0.0.2/p?q#f',
            ],
            'URI with scoped IP' => [
                'scheme://[fe80:1234::%251]/p?q#f',
                'scheme://[fe80:1234::%251]/p?q#f',
            ],
            'URI without authority' => [
                'scheme:path?query#fragment',
                'scheme:path?query#fragment',
            ],
            'URI without authority and scheme' => [
                '/path',
                '/path',
            ],
            'URI with empty host' => [
                'scheme:///path?query#fragment',
                'scheme:///path?query#fragment',
            ],
            'URI with empty host and without scheme' => [
                '///path?query#fragment',
                '///path?query#fragment',
            ],
            'URI without path' => [
                'scheme://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                'scheme://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
            ],
            'URI without path and scheme' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]?query#fragment',
            ],
            'URI without scheme with IPv6 host and port' => [
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?query#fragment',
                '//[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?query#fragment',
            ],
            'complete URI without scheme' => [
                '//user@[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?q#f',
                '//user@[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:42?q#f',
            ],
            'URI without authority and query' => [
                'scheme:path#fragment',
                'scheme:path#fragment',
            ],
            'URI with empty query' => [
                'scheme:path?#fragment',
                'scheme:path?#fragment',
            ],
            'URI with query only' => [
                '?query',
                '?query',
            ],
            'URI without fragment' => [
                'tel:05000',
                'tel:05000',
            ],
            'URI with empty fragment' => [
                'scheme:path#',
                'scheme:path#',
            ],
            'URI with fragment only' => [
                '#fragment',
                '#fragment',
            ],
            'URI with empty fragment only' => [
                '#',
                '#',
            ],
            'URI without authority 2' => [
                'path#fragment',
                'path#fragment',
            ],
            'URI with empty query and fragment' => [
                '?#',
                '?#',
            ],
            'URI with absolute path' => [
                '/?#',
                '/?#',
            ],
            'URI with absolute authority' => [
                'https://thephpleague.com./p?#f',
                'https://thephpleague.com./p?#f',
            ],
            'URI with absolute path only' => [
                '/',
                '/',
            ],
            'URI with empty query only' => [
                '?',
                '?',
            ],
            'relative path' => [
                '../relative/path',
                '../relative/path',
            ],
            'complex authority' => [
                'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
            ],
            'complex authority without scheme' => [
                '//a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
                '//a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com',
            ],
            'single word is a path' => [
                'http',
                'http',
            ],
            'URI scheme with an empty authority' => [
                'http://',
                'http://',
            ],
            'single word is a path, no' => [
                'http:::/path',
                'http:::/path',
            ],
            'fragment with pseudo segment' => [
                'http://example.com#foo=1/bar=2',
                'http://example.com#foo=1/bar=2',
            ],
            'complex URI' => [
                'htà+d/s:totot',
                'htà+d/s:totot',
            ],
            'scheme only URI' => [
                'http:',
                'http:',
            ],
            'RFC3986 LDAP example' => [
                'ldap://[2001:db8::7]/c=GB?objectClass?one',
                'ldap://[2001:db8::7]/c=GB?objectClass?one',
            ],
            'RFC3987 example' => [
                'http://bébé.bé./有词法别名.zh',
                'http://bébé.bé./有词法别名.zh',
            ],
            'colon detection respect RFC3986 (1)' => [
                'http://example.org/hello:12?foo=bar#test',
                'http://example.org/hello:12?foo=bar#test',
            ],
            'colon detection respect RFC3986 (2)' => [
                '/path/to/colon:34',
                '/path/to/colon:34',
            ],
            'scheme with hyphen' => [
                'android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
                'android-app://org.wikipedia/http/en.m.wikipedia.org/wiki/The_Hitchhiker%27s_Guide_to_the_Galaxy',
            ],
        ];
    }

    #[DataProvider('invalidAuthorityComponents')]
    public function test_it_will_fails_reconstructing_the_uri_with_invalid_authority(array $components): void
    {
        $this->expectException(SyntaxError::class);

        UriString::buildAuthority($components);
    }

    public static function invalidAuthorityComponents(): iterable
    {
        yield 'missing host but has port' => [
            'components' => ['port' => 80],
        ];

        yield 'missing host but has user component' => [
            'components' => ['user' => 'foo'],
        ];

        yield 'missing host but has pass component' => [
            'components' => ['pass' => 'bar'],
        ];
    }

    #[DataProvider('resolveProvider')]
    public function testCreateResolve(string $baseUri, string $uri, string $expected): void
    {
        self::assertSame($expected, UriString::resolve($uri, $baseUri));
    }

    public static function resolveProvider(): array
    {
        return [
            'base uri'                => [self::BASE_URI, '',              self::BASE_URI],
            'scheme'                  => [self::BASE_URI, 'http://d/e/f',  'http://d/e/f'],
            'path 1'                  => [self::BASE_URI, 'g',             'http://a/b/c/g'],
            'path 2'                  => [self::BASE_URI, './g',           'http://a/b/c/g'],
            'path 3'                  => [self::BASE_URI, 'g/',            'http://a/b/c/g/'],
            'path 4'                  => [self::BASE_URI, '/g',            'http://a/g'],
            'authority'               => [self::BASE_URI, '//g',           'http://g'],
            'query'                   => [self::BASE_URI, '?y',            'http://a/b/c/d;p?y'],
            'path + query'            => [self::BASE_URI, 'g?y',           'http://a/b/c/g?y'],
            'fragment'                => [self::BASE_URI, '#s',            'http://a/b/c/d;p?q#s'],
            'path + fragment'         => [self::BASE_URI, 'g#s',           'http://a/b/c/g#s'],
            'path + query + fragment' => [self::BASE_URI, 'g?y#s',         'http://a/b/c/g?y#s'],
            'single dot 1'            => [self::BASE_URI, '.',             'http://a/b/c/'],
            'single dot 2'            => [self::BASE_URI, './',            'http://a/b/c/'],
            'single dot 3'            => [self::BASE_URI, './g/.',         'http://a/b/c/g/'],
            'single dot 4'            => [self::BASE_URI, 'g/./h',         'http://a/b/c/g/h'],
            'double dot 1'            => [self::BASE_URI, '..',            'http://a/b/'],
            'double dot 2'            => [self::BASE_URI, '../',           'http://a/b/'],
            'double dot 3'            => [self::BASE_URI, '../g',          'http://a/b/g'],
            'double dot 4'            => [self::BASE_URI, '../..',         'http://a/'],
            'double dot 5'            => [self::BASE_URI, '../../',        'http://a/'],
            'double dot 6'            => [self::BASE_URI, '../../g',       'http://a/g'],
            'double dot 7'            => [self::BASE_URI, '../../../g',    'http://a/g'],
            'double dot 8'            => [self::BASE_URI, '../../../../g', 'http://a/g'],
            'double dot 9'            => [self::BASE_URI, 'g/../h' ,       'http://a/b/c/h'],
            'mulitple slashes'        => [self::BASE_URI, 'foo////g',      'http://a/b/c/foo////g'],
            'complex path 1'          => [self::BASE_URI, ';x',            'http://a/b/c/;x'],
            'complex path 2'          => [self::BASE_URI, 'g;x',           'http://a/b/c/g;x'],
            'complex path 3'          => [self::BASE_URI, 'g;x?y#s',       'http://a/b/c/g;x?y#s'],
            'complex path 4'          => [self::BASE_URI, 'g;x=1/./y',     'http://a/b/c/g;x=1/y'],
            'complex path 5'          => [self::BASE_URI, 'g;x=1/../y',    'http://a/b/c/y'],
            'dot segments presence 1' => [self::BASE_URI, '/./g',          'http://a/g'],
            'dot segments presence 2' => [self::BASE_URI, '/../g',         'http://a/g'],
            'dot segments presence 3' => [self::BASE_URI, 'g.',            'http://a/b/c/g.'],
            'dot segments presence 4' => [self::BASE_URI, '.g',            'http://a/b/c/.g'],
            'dot segments presence 5' => [self::BASE_URI, 'g..',           'http://a/b/c/g..'],
            'dot segments presence 6' => [self::BASE_URI, '..g',           'http://a/b/c/..g'],
            'origin uri without path' => ['http://h:b@a', 'b/../y',        'http://h:b@a/y'],
            'not same origin'         => [self::BASE_URI, 'ftp://a/b/c/d', 'ftp://a/b/c/d'],
        ];
    }

    #[Test]
    #[DataProvider('invalidUriWithWhitespaceProvider')]
    public function it_fails_parsing_uri_string_with_whitespace(string $uri): void
    {
        $this->expectException(UriException::class);

        UriString::parse($uri);
    }

    public static function invalidUriWithWhitespaceProvider(): iterable
    {
        yield 'uri containing only whitespaces' => ['uri' => '     '];
        yield 'uri starting with whitespaces' => ['uri' => '    https://a/b?c'];
        yield 'uri ending with whitespaces' => ['uri' => 'https://a/b?c   '];
        yield 'uri surrounded by whitespaces' => ['uri' => '   https://a/b?c   '];
        yield 'uri containing with whitespaces' => ['uri' => 'https://a/b ?c'];
    }

    #[Test]
    #[DataProvider('normalizedUriProvider')]
    public function it_can_normalize_uri_string(string $uri, string $expected): void
    {
        self::assertSame($expected, UriString::normalize($uri));
    }

    public static function normalizedUriProvider(): iterable
    {
        yield 'URI is unchanged' => [
            'uri' => 'http://a/b/c',
            'expected' => 'http://a/b/c',
        ];

        yield 'URI scheme is normalized to lowercase' => [
            'uri' => 'HtTp://a/b/c',
            'expected' => 'http://a/b/c',
        ];

        yield 'URI host is normalized to lowercase' => [
            'uri' => 'HtTp://AaAa/b/c',
            'expected' => 'http://aaaa/b/c',
        ];

        yield 'URI path is partially decoded without affecting delimiter characters' => [
            'uri' => 'https://example.com/foo/bar%2Fbaz',
            'expected' => 'https://example.com/foo/bar%2Fbaz',
        ];

        yield 'URI query is partially decoded without affecting delimiter characters' => [
            'uri' => 'https://example.com?foo=bar%26baz%3Dqux',
            'expected' => 'https://example.com?foo=bar%26baz%3Dqux',
        ];

        yield 'URI IPv6 host is not compressed' => [
            'uri' => 'https://[fe80:0000:0000:0000:0000:0000:0000:000a%25en1]/foo/bar',
            'expected' => 'https://[fe80:0000:0000:0000:0000:0000:0000:000a%25en1]/foo/bar',
        ];

        yield 'URI let port unchanged' => [
            'uri' => 'https://foobar:443/foo/bar',
            'expected' => 'https://foobar:443/foo/bar',
        ];
    }

    public function test_it_does_resolves_uri_against_authority_less_absolte_path(): void
    {
        self::assertSame('foo:/c', UriString::resolve('../../c', 'foo:/a/b'));
    }
}
