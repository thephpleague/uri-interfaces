Uri Interfaces
=======

This package contains Interfaces to be use to represents URI objects and URI components objects according to [RFC 3986](http://tools.ietf.org/html/rfc3986).

System Requirements
-------

You need:

- **PHP >= 5.3.0** but the latest stable version of PHP is recommended

Install
--------

```
$ composer require league/uri-interfaces
```

Documentation
--------

The following interfaces are defined:

### 1. League\Uri\Interfaces\Component

This interface models URI Components as specified in [RFC 3986](http://tools.ietf.org/html/rfc3986). This interface provides methods for interacting with any type of URI components in a predicable way.  It also specifies a `__toString()` method for casting the modeled URI component to its string representation.


### 1.2 League\Uri\Interfaces\CollectionComponent

This interface extends:

- The `Countable` Interface
- The `IteratorAggregate` Interface
- The `Component` Interface

And provides extra methods for filtering and removing items from such components based on their index or values.

### 1.3 League\Uri\Interfaces\PathComponent

This interface extends the Component interface and provides extra methods to represent any type of Path component.

### 2. League\Uri\Interfaces\Uri

Uri interface models generic URIs as specified in [RFC 3986](http://tools.ietf.org/html/rfc3986). The interface provides methods for interacting with the various URI parts, which will obviate the need for repeated parsing of the URI. It also specifies a `__toString()` method for casting the modeled URI to its string representation. This interface exposes the same methods as `Psr\Http\Message\UriInterface`. But, unlike the `UriInterface`, this interface does not require the `http` and `https` schemes to be supported. The supported schemes are determined by the concrete class which implements this interface.

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