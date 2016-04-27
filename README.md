Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)
[![Reference Status](https://www.versioneye.com/php/gettext:gettext/reference_badge.svg?style=flat)](https://www.versioneye.com/php/gettext:gettext/references)
[![Latest Stable Version](https://poser.pugx.org/gettext/gettext/v/stable.svg)](https://packagist.org/packages/gettext/gettext)
[![Total Downloads](https://poser.pugx.org/gettext/gettext/downloads.svg)](https://packagist.org/packages/gettext/gettext)
[![Monthly Downloads](https://poser.pugx.org/gettext/gettext/d/monthly.png)](https://packagist.org/packages/gettext/gettext)
[![License](https://poser.pugx.org/gettext/gettext/license.svg)](https://packagist.org/packages/gettext/gettext)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47/big.png)](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com> (MIT License)

Gettext is a PHP (5.3) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

## Installation

With composer (recomended):

```
composer require gettext/gettext
```

If you don't use composer in your project, you have to download and place this package in a directory of your project. You need to install also [gettext/languages](https://github.com/mlocati/cldr-to-gettext-plural-rules). Then, include the autoloaders of both projects in any place of your php code:

```php
include_once "libs/gettext/src/autoloader.php";
include_once "libs/cldr-to-gettext-plural-rules/src/autoloader.php";
```

## Classes and functions

This package contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations
* `Gettext\Extractors\*` - Import translations from various sources (po, mo, php, js, etc)
* `Gettext\Generators\*` - Export translations to various formats (po, mo, php, json, etc)
* `Gettext\Translator` - To use the translations in your php templates instead the [gettext extension](http://php.net/gettext)
* `Gettext\GettextTranslator` - To use the [gettext extension](http://php.net/gettext)

## Usage example

```php
use Gettext\Translations;

//import from a .po file:
$translations = Translations::fromPoFile('locales/gl.po');

//edit some translations:
$translation = $translations->find(null, 'apple');

if ($translation) {
	$translation->setTranslation('Mazá');
}

//export to a php array:
$translations->toPhpArrayFile('locales/gl.php');

//and to a .mo file
$translations->toMoFile('Locale/gl/LC_MESSAGES/messages.mo');
```

If you want use this translations in your php templates without using the gettext extension:

```php
use Gettext\Translator;

//Create the translator instance
$t = new Translator();

//Load your translations (exported as PhpArray):
$t->loadTranslations('locales/gl.php');

//Use it:
echo $t->gettext('apple'); // "Mazá"

//If you want use global functions:
$t->register();

echo __('apple'); // "Mazá"

__e('apple'); // "Mazá"
```

To use this translations with the gettext extension:

```php
use Gettext\GettextTranslator;

//Create the translator instance
$t = new GettextTranslator();

//Set the language and load the domain
$t->setLanguage('gl');
$t->loadDomain('messages', 'Locale');

//Use it:
echo $t->gettext('apple'); // "Mazá"

//Or use the gettext functions
echo gettext('apple'); // "Mazá"

//If you want use the global functions
$t->register();

echo __('apple'); // "Mazá"
```

The benefits of using the functions provided by this library (`__()` instead `_()` or `gettext()`) are:

* You are using the same functions, no matter whether the translations are provided by gettext extension or any other method
* You can use variables easier because you have sprintf functionality. For example: `__('Hello %s', 'world')` instead `sprintf(_('Hello %s'), 'world')`.

## Translation

The `Gettext\Translation` class stores all information about a translation: the original text, the translated text, source references, comments, etc.

```php
// __construct($context, $original, $plural)
$translation = new Gettext\Translation('comments', 'One comment', '%s comments');

$translation->setTranslation('Un comentario');
$translation->setPluralTranslation('%s comentarios');

$translation->addReference('templates/comments/comment.php', 34);
$translation->addComment('To display the amount of comments in a post');

echo $translation->getContext(); // comments
echo $translation->getOriginal(); // One comment
echo $translation->getTranslation(); // Un comentario

// etc...
```

## Translations

The `Gettext\Translations` class stores a collection of translations:

```php
$translations = new Gettext\Translations();

//You can add new translations using the array syntax
$translations[] = new Gettext\Translation('comments', 'One comment', '%s comments');

//Or using the "insert" method
$insertedTranslation = $translations->insert('comments', 'One comments', '%s comments');

//Find a specific translation
$translation = $translations->find('comments', 'One comments');

//Edit headers, domain, etc
$translations->setHeader('Last-Translator', 'Oscar Otero');
$translations->setDomain('my-blog');
```

## Extractors

The extrators are classes that extract the gettext values from any source and return a `Gettext\Translations` instance with them. For example, to scan a .po file:

```php
//From a file
$translations = Gettext\Extractors\Po::fromFile('locales/en.po');

//From a string
$string = file_get_contents('locales/en.po');
$translations = Gettext\Extractors\Po::fromString($string);
```

The available extractors are the following:

* `Gettext\Extractors\Po` - Gets the strings from PO
* `Gettext\Extractors\Mo` - Gets the strings from MO
* `Gettext\Extractors\PhpCode` - To scan a php file looking for all gettext functions (see `translator_functions.php`)
* `Gettext\Extractors\JsCode` - To scan a javascript file looking for all gettext functions (the same than PhpCode but for javascript)
* `Gettext\Extractors\PhpArray` - To get the translations from a php file that returns an array
* `Gettext\Extractors\Jed` - To scan a json file compatible with the [Jed library](http://slexaxton.github.com/Jed/)
* `Gettext\Extractors\Blade` - To scan a Blade template (For laravel users. Thanks [@eusonlito](https://github.com/eusonlito))
* `Gettext\Extractors\Twig` - To scan a Twig template (Thanks [@exnor](https://github.com/exnor))
* `Gettext\Extractors\JsonDictionary` - To get translations from a plain json file with the format `{"original": "translation"}`
* `Gettext\Extractors\YamlDictionary` - To get translations from a plain yaml file with the format `"original": translation`
* `Gettext\Extractors\CsvDictionary` - Gets the strings from plain CSV with the format `"original", "translation"`


## Generators

The generators export a `Gettext\Translations` instance to any format (po, mo, array, etc).

```php
//Save to a file
Gettext\Generators\Po::toFile($translations, 'locales/en.po');

//Return as a string
$content = Gettext\Generators\Po::toString($translations);
$string = file_put_contents('locales/en.po', $content);
```

The available generators are:

* `Gettext\Generators\Mo` - Exports to Mo format
* `Gettext\Generators\Po` - Exports to Po format
* `Gettext\Generators\PhpArray` - Exports to php code that returns an array with all values
* `Gettext\Generators\Jed` - Exports to json format compatible with [Jed library](http://slexaxton.github.com/Jed/)
* `Gettext\Generators\JsonDictionary` - Export to plain json with the format `{"original": "translation"}` (thanks, [@gator92](https://github.com/Gator92))
* `Gettext\Generators\YamlDictionary` - Export to plain yaml with the format `"original": translation` (thanks, [@sourcerer-mike](https://github.com/sourcerer-mike))
* `Gettext\Generators\CsvDictionary` - Exports to CSV format with the format `"original","translation"` (thanks, [@sourcerer-mike](https://github.com/sourcerer-mike))

To ease the work with generators and extractors you can use the magic methods availables in `Gettext\Translations` that import and export the translations in all these formats:

```php
use Gettext\Translations;

//Import the translations from a .po file
$translations = Translations::fromPoFile('locales/en.po');

//Add more translations from another .po file
$translations->addFromPoFile('locales/more-en.po');

//Export to .mo
$translations->toMoFile('locales/en.mo');
```

To import translations, the methods are static and named `from + [Extractor] + [File/String]`, for example `fromPhpArrayFile` or `fromJsCodeString`. To export or add more translations use the methods named `addFrom + [Generator] + [File/String]` (to add) or  `to + [Generator] + [File/String]` (to export) for example `addFromPhpCodeFile`, `toPhpArrayFile` or `toPoString`.

## Translator

The class `Gettext\Translator` implements the gettext functions in php. Useful if you don't have the native gettext extension for php or want to avoid problems with it. You can load the translations from a php array file or using a `Gettext\Translations` instance:

```php
use Gettext\Translator;

//Create a new instance of the translator
$t = new Translator();

//Load the translations using any of the following ways:

// 1. from php files (generated by Gettext\Extractors\PhpArray)
$t->loadTranslations('locales/gl.php');

// 2. using the array directly
$array = include 'locales/gl.php';
$t->loadTranslations($array);

// 3. using a Gettext\Translations instance (slower)
$translations = Gettext\Translations::fromPoFile('locales/gl.po');
$t->loadTranslations($translations);

//Now you can use it in your templates
echo $t->gettext('apple');
```

## GettextTranslator

The class `Gettext\GettextTranslator` uses the gettext extension. It's useful because combines the performance of using real gettext functions but with the same API than `Translator` class, so you can switch to one or other translator deppending of the environment without change code of your app.

```php
use Gettext\GettextTranslator;

//Create a new instance
$t = new GettextTranslator();

//It detects the environment variables to set the locale, but you can change it:
$t->setLanguage('gl');

//Load the domains:
$t->loadDomain('messages', 'project/Locale');
//this means you have the file "project/Locale/gl/LC_MESSAGES/messages.po"

//Now you can use it in your templates
echo $t->gettext('apple');
```

## Global functions

To ease the use of translations in your php templates, you can use the provided functions:

```php
//Register the translator to use the global functions
$t->register();

echo __('apple'); // it's the same than $t->gettext('apple');

__e('apple'); // it's the same than echo $t->gettext('apple');
```

You can scan the php files containing these functions and extract the values with the PhpCode extractor:

```html
<!-- index.php -->
<html>
	<body>
		<?php echo __('Hello world'); ?>
	</body>
</html>
```


# Merge translations

To work with different translations you may want merge them in an unique file. There are two ways to do this:

The simplest way is adding new translations:

```php
use Gettext\Translations;

$translations = Translations::fromPoFile('my-file1.po');
$translations->addFromPoFile('my-file2.po');
```

A more advanced way is merge two `Translations` instances:

```php
use Gettext\Translations;

//Create a new Translations instances with our translations.

$translations1 = Translations::fromPoFile('my-file1.po');
$translations2 = Translations::fromPoFile('my-file2.po');

//Merge one inside other:
$translations1->mergeWith($translations2);

//Now translations1 has all values
```

The second argument of `mergeWith` defines how the merge will be done. You can pass one or various of the following predefined constants:

* MERGE_ADD: Adds the translations from translations2 to translations1 if they not exists
* MERGE_REMOVE: Removes the translations in translations1 if they are not in translations2
* MERGE_OVERRIDE: Overrides the translations in translations1 if they are in translations2
* MERGE_HEADERS: Merges the headers from translations2 to translations 1
* MERGE_REFERENCES: Merges the references from translations2 to translations1
* MERGE_COMMENTS: Merges the comments from translations2 to translations1
* MERGE_LANGUAGE: Applies the language and plural forms of translations2 to translation1
* MERGE_PLURAL: Translations with the same id but one with plurals and other singular will be merged

Example:

```php
use Gettext\Translations;

//Scan the php code to find the latest gettext translations
$translations = Translations::fromPhpCodeFile('my-templates.php');

//Get the translations of the code that are stored in a po file
$poTranslations = Translations::fromPoFile('locale.po');

//Apply the translations from the po file to the translations, and merges header and comments but not references and without add or remove translations:
$translations->mergeWith($poTranslations, Translations::MERGE_HEADERS | Translations::MERGE_COMMENTS);

//Now save a po file with the result
$translations->toPoFile('locale.po');
```

Note, if the second argument is not defined, the default is `self::MERGE_ADD | self::MERGE_HEADERS | self::MERGE_COMMENTS | self::MERGE_REFERENCES | self::MERGE_PLURAL`

## Use from CLI

There's a Robo task to use this library from the command line interface: https://github.com/oscarotero/GettextRobo

## Contributors

* [@oscarotero](https://github.com/oscarotero) (Creator and maintainer)
* [@mlocati](https://github.com/mlocati) (Mo generator/extractor, languages, etc)
* [@esnoeijs](https://github.com/esnoeijs) (plural parser)
* [@leom](https://github.com/leom) (Jed fixes)
* [@eusonlito](https://github.com/eusonlito) (Blade extractor)
* [@exnor](https://github.com/exnor) (Twig extractor)
* [@vvh-empora](https://github.com/vvh-empora) (fixes)
* [and many more...](https://github.com/oscarotero/Gettext/graphs/contributors)
