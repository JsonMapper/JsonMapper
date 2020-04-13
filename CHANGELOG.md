# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
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