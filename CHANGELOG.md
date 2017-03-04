# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

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

[4.3.0]: https://github.com/oscarotero/Gettext/compare/v4.2.0...v4.3.0
