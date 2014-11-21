Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com> (MIT License)

Gettext is a PHP (5.3) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

## v.2.0

The 2.0 version has some changes in the API. See the changelog for more information:
https://github.com/oscarotero/Gettext/releases/tag/2.0


## Classes and functions

This package contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations
* `Gettext\Translator` - Emulate gettext functions in your php templates
* `Gettext\Extractors\*` - Extract gettext values from various sources
* `Gettext\Generators\*` - Generate gettext formats

And the file `translator_functions.php` that provide the gettext functions to use in your templates.


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
* `Gettext\Extractors\PhpCode` - To scan a php file looking for all gettext functions
* `Gettext\Extractors\JsCode` - To scan a javascript file looking for all gettext functions
* `Gettext\Extractors\PhpArray` - To get the translations from a php file that returns an array
* `Gettext\Extractors\Jed` - To scan a json file compatible with the Jed library
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
* `Gettext\Generators\Jed` - Exports to json format compatible with Jed library

## Usage example

First, lets scan a Po file:

```php
//Include the autoloader if you don't use composer or PSR-4 loader
include('myLibsFolder/Gettext/autoloader.php');

$translations = Gettext\Extractors\Po::fromFile('locales/gl.po');
```

Now, we can edit some translations:

```php
// find($context, $original, $plural)
$translation = $translations->find(null, 'apple');

if ($translation) {
	$translation->setTranslation('Mazá');
}
```

And use the Generators to export this entry to other formats like php array or json file (for use with Jed library: http://slexaxton.github.com/Jed/):

```php
Gettext\Generators\PhpArray::toFile($translations, 'locales/gl.php');
Gettext\Generators\Jed::toFile($translations, 'locales/gl.json');
```

A simpler and easier way to extract/generate translations is using the magic methods `from(Extractor)[String|File]` and `to[Generator](String|File)`:

```php
use Gettext\Translations;

$locales = Translations::fromPoFile('locale.po');
//This is the same than Gettext\Extractors\Po::fromFile('locale.po')

$locales->toMoFile('locale.mo');
//This is the same than Gettext\Generators\Mo::toFile('locale.mo')

//or to export into a string:
var_dump($locales->toMoString());
```


## Translator

The class `Gettext\Translator` implements the gettext functions in php. Usefult if you don't have the native gettext extension for php or want to avoid problems with it. The fastest way to use it is loading the translations from an array, so you have to use the `Gettext\Generators\PhpArray` generator.

```php
use Gettext\Translator;

//Create a new instance of the translator
$t = new Translator();

//Load the translations from php files (generated with PhpArray)
$t->loadTranslations('locales/gl.php');

//Now you can use it in your templates
echo $t->gettext('apple'); //Returns Mazá
```

The `loadTranslations` method accepts also arrays and `Gettext\Translations` instances:

```
//If you have your array already loaded:
$arrayTranslations = include 'my-translations.php';
$t->loadFromArray($arrayTranslations);


//If you have your translations in any other format (such .po) and don't want to export them to arrays:
$poTranslations = Gettext\Tranlations::fromPoFile('locales/gl.po');
$t->loadTranslations($poTranslations);
```

The translator functions ease the use of Gettext\Translator:

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

```php
use Gettext\Translations;

$translations = Translations::fromPhpCodeFile('index.php');

//Search for a string
$t = $translations->find(null, 'Hello world');

//Translate to other language
$t->setTranslation('Ola mundo');

//Exports the translations to .po file (for example)
$translations->toPoFile('locales/gl.po');

//And exports again the translations to .json file
$translations->toJedFile('locales/gl.json');
```

And use the translations exported to json in javascript with the Jed library (http://slexaxton.github.com/Jed/)

```javascript
$.getJSON('locales/gl.json', function (locale) {
	i18n = new Jed({
		locale_data: locale
	});

	alert(i18n.gettext('Hello world')); //alerts "Ola mundo"
});
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

* oscarotero (Creator and maintainer)
* esnoeijs (Contributed with the plural parser)
* leom (Contributed with some Jed fixes)
* eusonlito (Contributed with Blade extractor)

## TO-DO

* Working with domains
