# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [UNRELEASED]

### Added

* New option `noLocation` to po generator, to omit the references [#143](https://github.com/oscarotero/Gettext/issues/143)
* New options `delimiter`, `enclosure` and `escape_char` to Csv Extractor and Generator [#145](https://github.com/oscarotero/Gettext/pull/145/)

### Fixed

* Improved the code style

## [4.3.0] - 2017-03-04

### Added

* Added support for named placeholders (using `strtr`). For example:

  ```php
  __('Hello :name', [':name' => 'World']);
  ```
* Added support for Twig v2
* New function `BaseTranslator::includeFunctions()` to include the functions file without register any translator

### Fixed

* Fixed a bug related with the javascript source extraction with single quotes

---

Previous releases are documented in [github releases](https://github.com/oscarotero/Gettext/releases)

[UNRELEASED]: https://github.com/oscarotero/Gettext/compare/v4.3.0...master
[4.3.0]: https://github.com/oscarotero/Gettext/compare/v4.2.0...v4.3.0
