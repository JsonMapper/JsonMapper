# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Add support for mixed type [PR#39](https://github.com/JsonMapper/JsonMapper/pull/39)
### Changed
- Improved internal representation of scalar types, introducing ScalarType Enum class. [PR#34](https://github.com/JsonMapper/JsonMapper/pull/34)
## Fixed
- Fix mapping to a class from the same namespace when using PHP 7.4 namespace is prefixed twice. [PR#41](https://github.com/JsonMapper/JsonMapper/pull/41)

## [1.2.0] - 2020-07-12
### Added
- Introduce pop, unshift, shift, remove, removeByName methods to the JsonMapperInterface [PR#32](https://github.com/JsonMapper/JsonMapper/pull/32)
### Fixed
- Resolved several issues found by PHPStan [PR#29](https://github.com/JsonMapper/JsonMapper/pull/29)
- Properties marked as array are casted to enable object to array mapping [PR#36](https://github.com/JsonMapper/JsonMapper/pull/36)
### Changed
- Reduced a single used helper splitting into the core and into the doc block middleware. [PR#30](https://github.com/JsonMapper/JsonMapper/pull/30)

## [1.1.0] - 2020-05-29
### Added 
- Support for arrays using square bracket notation (e.g. User[]) in DocBlockAnnotations middleware. (PR#27/#28)

## [1.0.1] - 2020-05-04
### Fixed
- Case conversion removing attribute when replacement key is same as the original key

## [1.0.0] - 2020-04-23
### Added
- New Debugger middleware to help debug the in between middleware
- Caching support to the DocBlockAnnotations and TypedProperties middleware

## [0.3.0] - 2020-04-13
### Added 
- New FinalCallback middleware to invoke a final callback when mapping is completed.
- New CaseConversion middleware to handle difference between text notation in JSON and object

## [0.2.1] - 2020-03-25
### Fixed
- Correct badge urls in readme

## [0.2.0] - 2020-03-25
### Changed
- Changed top level namespace 

## [0.1.0] - 2020-03-25
### Added
- Factory for easy creation of new JsonMapper instance 
### Changed
- Replaced strategies with middleware to allow chaining of multiple middleware to increase configuration
- Readme was updated to reflect the usage and customizing of JsonMapper
- Updated license to MIT

## [0.0.2] - 2020-03-22
### Added
- Support custom classes with recursion
- Support for custom classes with imported namespace
- Support to map an array of objects
### Fixed
- Fixed missing coveralls dependency
- Cleanup strategies from duplication

## [0.0.1] - 2020-03-15
### Added
- Add PHP 7.4 typed properties based strategy
- Add DocBlock based strategy
- Add support for DateTime types
- Add typecasting
- Add value setting logic based on strategy 