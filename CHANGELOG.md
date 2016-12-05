# Changelog

All Notable changes to `League\Uri\Interfaces` will be documented in this file

## Next

### Added

- `League\Uri\Interfaces\PathComponent::isEmpty`

### Fixed

- None

### Deprecated

- None

### Removed

- `League\Uri\Interfaces\CollectionComponent`

## 0.3.0 - 2016-12-01

### Added

- `League\Uri\Interfaces\Component::NO_ENCODING` to remove any specific encoding
- `League\Uri\Interfaces\Component::RFC3986_ENCODING` to specify encoding according to RFC3986 rules
- `League\Uri\Interfaces\Component::RFC3987_ENCODING` to specify encoding according to RFC3987 rules

### Fixed

- Update `Component::getContent` optional parameter default.

### Deprecated

- None

### Removed

- `League\Uri\Interfaces\Component::RFC3986`
- `League\Uri\Interfaces\Component::RFC3987`

## 0.2.0 - 2016-11-29

### Added

- `League\Uri\Interfaces\Component::RFC3986` to specify encoding according to RFC3986 rules
- `League\Uri\Interfaces\Component::RFC3987` to specify encoding according to RFC3987 rules

### Fixed

- `League\Uri\Interfaces\Component::getContent` now takes an optional `$enc_type` parameter
to specify the returned content encoding rules.
- `League\Uri\Interfaces\Uri` docblocks simplified around Exception thrown

### Deprecated

- None

### Removed

- None

## 0.1.0 - 2016-10-17

### Added

- `League\Uri\Interfaces\Component::getContent`
- `League\Uri\Interfaces\Component::withContent`
- `League\Uri\Interfaces\Component::isDefined`

### Fixed

- Renamed `League\Uri\Interfaces\Collection` to `League\Uri\Interfaces\CollectionComponent`
- Renamed `League\Uri\Interfaces\Path` to `League\Uri\Interfaces\PathComponent`

### Deprecated

- None

### Removed

- `League\Uri\Interfaces\UriPart`
- `League\Uri\Interfaces\HierarchicalComponent`
- `League\Uri\Interfaces\Scheme`
- `League\Uri\Interfaces\User`
- `League\Uri\Interfaces\Pass`
- `League\Uri\Interfaces\UserInfo`
- `League\Uri\Interfaces\Host`
- `League\Uri\Interfaces\Port`
- `League\Uri\Interfaces\Path::withoutEmptySegments`
- `League\Uri\Interfaces\Path::hasTrailingSlash`
- `League\Uri\Interfaces\Path::withTrailingSlash`
- `League\Uri\Interfaces\Path::withoutTrailingSlash`
- `League\Uri\Interfaces\Path::getTypecode`
- `League\Uri\Interfaces\Path::withTypecode`
- `League\Uri\Interfaces\Path::FTP_TYPE_ASCII`
- `League\Uri\Interfaces\Path::FTP_TYPE_BINARY`
- `League\Uri\Interfaces\Path::FTP_TYPE_DIRECTORY`
- `League\Uri\Interfaces\Path::FTP_TYPE_EMPTY`
- `League\Uri\Interfaces\HierarchicalPath`
- `League\Uri\Interfaces\DataPath`
- `League\Uri\Interfaces\Query`
- `League\Uri\Interfaces\Fragment`
