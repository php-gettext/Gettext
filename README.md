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

Gettext is a PHP (>=5.4) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

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
* You can use variables easier because sprintf functionality is included. For example: `__('Hello %s', 'world')` instead `sprintf(_('Hello %s'), 'world')`.

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

The extrators allows to fetch gettext values from any source. For example, to scan a .po file:

```php
$translations = new Gettext\Translations();

//From a file
Gettext\Extractors\Po::fromFile('locales/en.po', $translations);

//From a string
$string = file_get_contents('locales2/en.po');
Gettext\Extractors\Po::fromString($string, $translations);
```

The better way to use extractors is using the magic methods of `Gettext\Translations`:

```php
//Create a Translations instance using a po file
$translations = Gettext\Translations::fromPoFile('locales/en.po');

//Add more messages from other files
$translations->addFromPoFile('locales2/en.po');
```

The available extractors are the following:

Name | Description | Example
---- | ----------- | --------
**Po**             | Gets the messages from PO. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Po.po)
**Mo**             | Gets the messages from MO. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Mo.mo)
**PhpCode**        | Scans php code looking for all gettext functions (see `translator_functions.php`). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/2/Input.PhpCode.php)
**JsCode**         | Scans javascript code looking for all gettext functions (the same than PhpCode but for javascript). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/8/Input.JsCode.js)
**PhpArray**       | Gets the messages from a php file that returns an array. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/PhpArray.php)
**Jed**            | Gets the messages from a json compatible with [Jed](http://slexaxton.github.com/Jed/). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Jed.json)
**Blade**          | Scans a Blade template (For laravel users. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/4/Input.Blade.php)
**Twig**           | To scan a Twig template. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/6/Input.Twig.php)
**JsonDictionary** | Gets the messages from a json (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/JsonDictionary.json)
**YamlDictionary** | Gets the messages from a yaml (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/YamlDictionary.yml)
**CsvDictionary**  | Gets the messages from csv (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/CsvDictionary.csv)
**Csv**            | Gets the messages from csv. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Csv.csv)
**Yaml**           | Gets the messages from yaml. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Yaml.yml)
**Xliff**          | Gets the messages from [xliff (2.0)](http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Xliff.xlf)

## Generators

The generators export a `Gettext\Translations` instance to any format (po, mo, array, etc).

```php
//Save to a file
Gettext\Generators\Po::toFile($translations, 'locales/en.po');

//Return as a string
$content = Gettext\Generators\Po::toString($translations);
file_put_contents('locales/en.po', $content);
```

Like extractors, the better way to use generators is using the magic methods of `Gettext\Translations`:

```php
//Extract messages from a php code file
$translations = Gettext\Translations::fromPhpCodeFile('templates/index.php');

//Export to a po file
$translations->toPoFile('locales/en.po');

//Export to a po string
$content = $translatons->toPoString();
file_put_contents('locales/en.po', $content);
```

The available generators are the following:

Name | Description | Example
---- | ----------- | --------
**Po**             | Exports to Po. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Po.po)
**Mo**             | Exports to Mo. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Mo.mo)
**PhpArray**       | Exports to php code that returns an array. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/PhpArray.php)
**Jed**            | Exports to json format compatible with [Jed](http://slexaxton.github.com/Jed/). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Jed.json)
**JsonDictionary** | Exports to json (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/JsonDictionary.json)
**YamlDictionary** | Exports to yaml (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/YamlDictionary.yml)
**CsvDictionary**  | Exports to csv (without plurals and context). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/CsvDictionary.csv)
**Csv**            | Exports to csv. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Csv.csv)
**Yaml**           | Exports to yaml. | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Yaml.yml)
**Xliff**          | Exports to [xliff (2.0)](http://docs.oasis-open.org/xliff/xliff-core/v2.0/os/xliff-core-v2.0-os.html). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Yaml.yml). | [example](https://github.com/oscarotero/Gettext/blob/master/tests/assets/1/Xliff.xlf)

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

Note, if the second argument is not defined, the default is `self::MERGE_ADD | self::MERGE_HEADERS | self::MERGE_COMMENTS | self::MERGE_REFERENCES | self::MERGE_PLURAL | self::MERGE_OVERRIDE`

## Use from CLI

There's a Robo task to use this library from the command line interface: https://github.com/oscarotero/GettextRobo

## Contributors

Thanks to all [contributors](https://github.com/oscarotero/Gettext/graphs/contributors) specially to [@mlocati](https://github.com/mlocati).
