# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

Previous releases are documented in [github releases](https://github.com/oscarotero/Gettext/releases)

## [5.2.0] - 2019-11-25
### Added
- New function `CodeScanner::extractCommentsStartingWith()` to extract comments from the code.

## [5.1.0] - 2019-11-11
### Added
- New function `CodeScanner::ignoreInvalidFunctions()` to ignore invalid functions instead throw an exception

## 5.0.0 - 2019-11-04
### Added
- New interfaces: `ScannerInterface` and `FunctionsScannerInterface`.

### Changed
- Moved the package and dependencies to [php-gettext](https://github.com/php-gettext) organization
- Minimum PHP version supported is 7.2
- Added php7 strict typing
- Extractors have been split into two different types of classes to import translations:
  - Scanners: To scan code files (like php, javascript, twig, etc) in order to collect gettext entries from many domains at the same time.
  - Loaders: To load a translation format such po, mo, json, xliff, etc
- Split the `Translation` and `Translations` classes in different sub-classes to handle comments, flags, references, etc. For example, instead `$translation->addComment('foo')` now it's `$translation->getComments()->add('foo')`.
- Simplified the options to merge translations with pre-configured options like `Merged::SCAN_AND_LOAD`.
- The headers of translations are always sorted alphabetically.
- Changed the signature of all classes and interfaces.

### Removed
- Extractors (now scanners and loaders), generators and translators were removed from this package and published as external packages, allowing to install only those that you need. Only Po and Mo formats are included by default.
- Removed magic classes like `Translations::fromPoFile` or `$translation->toMoFile()`. Now, the scanners, loaders and generators are independent classes that have to be instantiated.
- Removed `Merge::LANGUAGE_OVERRIDE` and `Merge::DOMAIN_OVERRIDE` contants

### Fixed
- Improved code quality
- The library is easier to extend
- Translation id can be independent of the context + original values, in order to be more compatible with Xliff format.

[5.2.0]: https://github.com/php-gettext/Gettext/compare/v5.1.0...HEAD
[5.1.0]: https://github.com/php-gettext/Gettext/compare/v5.0.0...v5.1.0
