Uri Interfaces
=======

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Version](https://img.shields.io/github/release/thephpleague/uri-interfaces.svg?style=flat-square)](https://github.com/thephpleague/uri-interfaces/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/league/uri-interfaces.svg?style=flat-square)](https://packagist.org/packages/league/uri-interfaces)

This package contains an interface to represents URI objects according to [RFC 3986](http://tools.ietf.org/html/rfc3986).

> ⚠️ this is a sub-split, for development, pull requests and issues, visit: https://github.com/thephpleague/uri-src


System Requirements
-------

You require **PHP >= 8.1** but the latest stable version of PHP is recommended

Handling of an IDN host requires the presence of the `intl`
extension or a polyfill for the `intl` IDN functions like the
`symfony/polyfill-intl-idn` otherwise an exception will be thrown
when attempting to validate or interact with such a host.

IPv4 conversion requires at least one of the following:

- the `GMP` extension,
- the `BCMatch` extension or
- a `64-bits` PHP version

otherwise an exception will be thrown when attempting to convert a host
as an IPv4 address.

Install
--------

```
$ composer require league/uri-interfaces
```

Documentation
--------

Full documentation can be found at [uri.thephpleague.com](http://uri.thephpleague.com).

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information. 
