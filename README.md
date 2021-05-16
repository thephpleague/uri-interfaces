Uri Interfaces
=======

This package contains an interface to represents URI objects according to [RFC 3986](http://tools.ietf.org/html/rfc3986).

System Requirements
-------

You need:

- **PHP >= 7.2** but the latest stable version of PHP is recommended

Install
--------

```
$ composer require league/uri-interfaces
```

Documentation
--------

### League\Uri\Contract\UriInterface

The `UriInterface` interface models generic URIs as specified in [RFC 3986](http://tools.ietf.org/html/rfc3986).
The interface provides methods for interacting with the various URI parts, which will obviate the need for repeated parsing of the URI.
It also specifies:
 
 - a `__toString()` method for casting the modeled URI to its string representation.
 - a `jsonSerialize()` method to improve interoperability with [WHATWG URL Living standard](https://url.spec.whatwg.org/)

#### Accessing URI properties

The `UriInterface` interface defines the following methods to access the URI string representation, its individual parts and components.

~~~php
<?php

public UriInterface::__toString(void): string
public UriInterface::jsonSerialize(void): string
public UriInterface::getScheme(void): ?string
public UriInterface::getUserInfo(void): ?string
public UriInterface::getHost(void): ?string
public UriInterface::getPort(void): ?int
public UriInterface::getAuthority(void): ?string
public UriInterface::getPath(void): string
public UriInterface::getQuery(void): ?string
public UriInterface::getFragment(void): ?string
~~~

#### Modifying URI properties

The `Uri` interface defines the following modifying methods. these methods **must** be implemented such that they retain the internal state of the current instance and return an instance that contains the changed state.

Delimiter characters are not part of the URI component and **must not** be added to the modifying method submitted value. If present they will be treated as part of the URI component content.

**These methods will trigger a `League\Uri\Contract\UriException` exception if the resulting URI is not valid. The validation is scheme dependent.**

~~~php
<?php

public UriInterface::withScheme(?string $scheme): self
public UriInterface::withUserInfo(?string $user [, string $password = null]): self
public UriInterface::withHost(?string $host): self
public UriInterface::withPort(?int $port): self
public UriInterface::withPath(string $path): self
public UriInterface::withQuery(?string $query): self
public UriInterface::withFragment(?string $fragment): self
~~~

#### Relation with [PSR-7](http://www.php-fig.org/psr/psr-7/#3-5-psr-http-message-uriinterface)

This interface exposes the same methods as `Psr\Http\Message\UriInterface`. But, differs on the following keys:

- This interface does not require the `http` and `https` schemes to be supported.
- Setter and Getter component methods, with the exception of the path component, accept and can return the `null` value.
- If no scheme is present, you are not required to fallback to `http` and `https` schemes specific validation rules.

### League\Uri\Contract\UriComponentInterface

The `UriComponentInterface` interface models generic URI components as specified in [RFC 3986](http://tools.ietf.org/html/rfc3986). The interface provides methods for interacting with an URI component, which will obviate the need for repeated parsing of the URI component. It also specifies a `__toString()` method for casting the modeled URI component to its string representation.

#### Accessing URI properties

The `UriComponentInterface` interface defines the following methods to access the URI component content.

~~~php
<?php

public UriComponentInterface::__toString(void): string
public UriComponentInterface::getContent(void): ?string
public UriComponentInterface::getUriComponent(void): ?string
public UriComponentInterface::jsonSerialize(void): ?string
~~~

#### Modifying URI properties

The `UriComponentInterface` interface defines the following modifying method. this method **must** be implemented such that it retains the internal state of the current instance and return an instance that contains the changed state.

~~~php
<?php

public UriComponentInterface::withContent(?string $content): static
~~~

### UriComponentInterface extended interfaces

Because each URI component has specific needs most have specialized interface which all extends the `UriComponentInterface` interface. The following interfaces also exist:

- `League\Uri\Contract\AuthorityInterface`
- `League\Uri\Contract\DataPathInterface`
- `League\Uri\Contract\DomainHostInterface`
- `League\Uri\Contract\FragmentInterface`
- `League\Uri\Contract\UserInfoInterface`
- `League\Uri\Contract\HostInterface`
- `League\Uri\Contract\IpHostInterface`
- `League\Uri\Contract\PathInterface`
- `League\Uri\Contract\PortInterface`
- `League\Uri\Contract\QueryInterface`
- `League\Uri\Contract\SegmentedPathInterface`

Contributing
-------

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

Security
-------

If you discover any security related issues, please email nyamsprod@gmail.com instead of using the issue tracker.

Credits
-------

- [ignace nyamagana butera](https://github.com/nyamsprod)
- [All Contributors](https://github.com/thephpleague/uri/contributors)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.
