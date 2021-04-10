# Changelog

All notable changes to `mocker` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
- [] add object get requests return value
- [] improve logical storage of data.
- [] Wrap results in Mocked instance

## [1.1.4] - 14.06.20
### Fixed
- Variables are no longer polluted between variable calls. [#6](https://github.com/reecem/mocker/issues/6)
- When calling an object that has an array assigned it will return whole the array as a string, see for more [#5](https://github.com/reecem/mocker/issues/5)

- See pull request [#15](https://github.com/ReeceM/mocker/pull/15)

### Note
There is a slight bug introduced with this version as to the fact that the arrays returned after the first step are arrays and not instances of Mocked. Cannot assign values after assigning the first time.

## [1.1.3] - 09.03.20
### Added
- Add support for laravel 7.0, See merge #13, @blakehooks

## [1.1.2] - 09.09.19
### Added
- Support for laravel 6
- Updated test methods for phpunit

## [1.1.1] - 08.05.19
### Added
- add support for array calls
- make the mocked class extend `ArrayObject`

### Features
- possibility to make mixed calls to a variable `$data->obj['arrayvalue']->anotherObject`

## [1.1.0] - 04.05.19
### Added
- This release adds a more readable output to the __toString() magic method, this is from #1 feature request.

## [1.0.2] - 30.04.19
### Changed
- requirements for the illuminate packages to support laravel/* ^5.6

## [1.0.1] - 29.04.19
### Fixes
- Fix composer.json

## [1.0.0] - 29.04.19

First release of Mocker - [Initial Release](https://github.com/ReeceM/mocker/releases/tag/v1.0)


[Unreleased]: https://github.com/ReeceM/mocker/compare/v1.1.4...HEAD
[1.1.4]: https://github.com/ReeceM/mocker/compare/v1.1.3...v1.1.4
[1.1.3]: https://github.com/ReeceM/mocker/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/ReeceM/mocker/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/ReeceM/mocker/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/ReeceM/mocker/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/ReeceM/mocker/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/ReeceM/mocker/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/ReeceM/mocker/releases/tag/v1.0.0

---
#### Help

- `Added` for new features.
- `Changed` for changes in existing functionality.
- `Deprecated` for soon-to-be removed features.
- `Removed` for now removed features.
- `Fixed` for any bug fixes.
- `Security` in case of vulnerabilities.
