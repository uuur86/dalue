# Changelog

All notable changes to this project are documented in this file.
The project follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Breaking changes

- Requires `uuur86/strobj` `^3.0` and PHP 7.4 or later. ([#1])
- `Mapper::map()` and `Mapper::mergeColumns()` declare an `array` return type.
- Missing paths return `null` (or the new `$default` argument) for every StrObj
  behavior. Stored `false` values are no longer turned into `''`. ([#3])
- `mergeColumns()` keeps every row of the longest column and fills missing cells
  with `null`. ([#4])

### Added

- Optional fourth argument `$default` for `Mapper::map()`. ([#3])
- `Mapper::DATA_PREFIX` and `Mapper::COLLECTION_SUFFIX` constants.
- PHPUnit test suite, PHPStan (level 8), PHP_CodeSniffer (PSR-12) and a GitHub
  Actions CI matrix for PHP 7.4 – 8.5. ([#5])
- Runnable examples in `examples/` with tested output and a `composer examples`
  script. ([#8])
- Wiki pages in `docs/wiki/`, published by GitHub Actions. ([#7])
- `CHANGELOG.md`, `CONTRIBUTING.md`, `SECURITY.md`, `.gitattributes` and
  `.gitignore`. ([#6])

### Fixed

- Schema keys such as `0`, `"0"` or `""` no longer stop the mapping. ([#2])
- Collection rows stay aligned when an item lacks a field. ([#4])
- Only keys that end with `[]` are treated as collections. ([#4])

### Removed

- The unused `ext-json` and `ext-mbstring` requirements and the unused
  `symfony/console` and `friendsofphp/php-cs-fixer` development dependencies.

## [0.1.1-beta]

- Initial public release.

[Unreleased]: https://github.com/uuur86/dalue/compare/v0.1.1-beta...HEAD
[0.1.1-beta]: https://github.com/uuur86/dalue/releases/tag/v0.1.1-beta
[#1]: https://github.com/uuur86/dalue/issues/1
[#2]: https://github.com/uuur86/dalue/issues/2
[#3]: https://github.com/uuur86/dalue/issues/3
[#4]: https://github.com/uuur86/dalue/issues/4
[#5]: https://github.com/uuur86/dalue/issues/5
[#6]: https://github.com/uuur86/dalue/issues/6
[#7]: https://github.com/uuur86/dalue/issues/7
[#8]: https://github.com/uuur86/dalue/issues/8
