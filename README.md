Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com> (MIT License)

Gettext is a PHP (5.3) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

## v.2.0

The 2.0 version has some changes in the API. See the changelog for more information:
https://github.com/oscarotero/Gettext/releases/tag/2.0


## Usage example

```php
//Include the autoloader if you don't use composer or PSR-4 loader
include('src/autoloader.php');

//scan a Po file:
$translations = Gettext\Translations::fromPoFile('locales/gl.po');

//edit some translations:
$translation = $translations->find(null, 'apple');

if ($translation) {
	$translation->setTranslation('Mazá');
}

//Export to PhpArray:
$translations->toPhpArrayFile('locales/gl.php');

//Create a translator for your php templates
$t = new Gettext\Translator();

//Load your translations:
$t->loadTranslations('locales/gl.php');

//Use it:
echo $t->gettext('apple'); //echoes "Mazá"

//Use the global functions:
__currentTranslator($t);

//Use it:
echo __('apple'); //echoes "Mazá"

__e('apple'); //echoes "Mazá"
```

## Classes and functions

This package contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations
* `Gettext\Translator` - Emulate gettext functions in your php templates
* `Gettext\Extractors\*` - Extract gettext values from various sources
* `Gettext\Generators\*` - Generate gettext formats

And the file `translator_functions.php` that provide the gettext functions to use in your templates.


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

//You can add new tranlations using the array syntax
$tranlations[] = new Gettext\Translation('comments', 'One comment', '%s comments');

//Or using the "insert" method
$insertedTranslation = $translations->insert('comments', 'One comments', '%s comments');

//Find a specific translation
$translation = $translations->find('comments', 'One comments', '%s comments');

//Edit headers, the domain value, etc
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
* `Gettext\Extractors\Blade` - To scan a Blade template (For laravel users. Thanks @eusonlito)

## Generators

The generators export a `Gettext\Translations` instance in any format (po, mo, array, etc).

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

To ease the work with generators and extractors you can use the magic methods availables in `Gettext\Translations` that import and export the translations in all these formats:

```php
use Gettext\Translations;

//Import the translations from a .po file
$translations = Translations::fromPoFile('locales/en.po');

//Export to .mo
$translations->toMoFile('locales/en.mo');
```

To import translations, the methods are static and named `from + [Extractor] + [File/String]`, for example `fromPhpArrayFile` or `fromJsCodeString`. To export use the methods named `to + [Generator] + [File/String]` for example `toPhpArrayFile` or `toPoString`.

## Translator

The class `Gettext\Translator` implements the gettext functions in php. Usefult if you don't have the native gettext extension for php or want to avoid problems with it. You can load the tranlations from a php array file or using a `Gettext\Tranlations` instance:

```php
use Gettext\Translator;

//Create a new instance of the translator
$t = new Translator();

//Load the translations using one of the following ways:

// 1. from php files (generated with PhpArray)
$t->loadTranslations('locales/gl.php');

// 2. using the array directly
$array = include 'locales/gl.php';
$t->loadTranslations($array);

// 3. using a Gettext\Translations instance (slower)
$translations = Gettext\Tranlations::fromPoFile('locales/gl.po');
$t->loadTranslations($translations);

//Now you can use it in your templates
echo $t->gettext('apple');
```

To ease the use of translations in your php templates, you can use the provided functions:

```php
//First set the translator instance as current translator:
__currentTranslator($t);

echo __('apple'); //Returns Mazá

__e('apple'); //echo Mazá
```

You can scan the php files that uses these functions and extract the values with the PhpCode extractor:

```html
<!-- index.php -->
<html>
	<body>
		<?php echo __('Hello world'); ?>
	</body>
</html>
```


# Merging translations

To work with different translations you may want merge them in an unique file. There is a way to do this:

```php
//Create a new Translations instances with our translations.

$translations1 = Gettext\Extractors\Po::fromFile('my-file1.po');
$translations2 = Gettext\Extractors\Po::fromFile('my-file2.po');

//Merge one inside other:
$translations1->mergeWith($translations2);

//Now translations1 has all values
```

The second argument of `mergeWith` defines how the merge will be done. You can pass one or various of the following predefined constants:

* MERGE_ADD: Adds the translations from translations2 to translations1 if they not exists
* MERGE_REMOVE: Removes the translations in translations1 if they are not in translations2
* MERGE_HEADERS: Merges the headers from translations2 to translations 1
* MERGE_REFERENCES: Merges the references from translations2 to translations1
* MERGE_COMMENTS: Merges the comments from translations2 to translations1

Example:

```php
use Gettext\Translations;

//Scan the php code to find the latest gettext translations
$translations = Gettext\Extractors\PhpCode::fromFile('my-templates.php');

//Get the translations of the code that are stored in a po file
$poTranslations = Gettext\Extractors\Po::fromFile('locale.po');

//Apply the translations from the po file to the translations, and merges header and comments but not references and without add or remove translations:
$translations->mergeWith($poTranslations, Translations::MERGE_HEADERS | Translations::MERGE_COMMENTS);

//Now save a po file with the result
Gettext\Generators\Po::generateFile($translations, 'locale.po');
```

Note, if the second argument is not defined, the default is `self::MERGE_ADD | self::MERGE_HEADERS | self::MERGE_COMMENTS`


## Contributors

* [oscarotero](https://github.com/oscarotero) (Creator and maintainer)
* [esnoeijs](https://github.com/esnoeijs) (plural parser)
* [leom](https://github.com/leom) (Jed fixes)
* [eusonlito](https://github.com/eusonlito) (Blade extractor)
* [vvh-empora](https://github.com/vvh-empora) (fixes)

## TO-DO

* Working with domains
