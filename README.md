Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com>

MIT License

Gettext is a PHP library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

Features:

* Written in php 5.3
* Easily extensible

Contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations

There are also two groups of clases: Extractors and Generators.


Extractors
----------

The extrators are classes that extract the gettext values from any source and return a `Gettext\Translations` instance with them. For example, to scan a .po file:

```php
//From a file
$translations = Gettext\Extractors\Po::fromFile('locales/en.po');

//From a string
$string = file_get_contents('locales/en.po');
$translations = Gettext\Extractors\Po::fromString($string);
```

The available extractors are the following:

* `Gettext\Extractors\PhpCode` - To scan a php file looking for all gettext functions
* `Gettext\Extractors\Jed` - To scan a json file compatible with the Jed library
* `Gettext\Extractors\JsCode` - To scan a javascript file looking for all gettext functions
* `Gettext\Extractors\PhpArray` - To get the translations from a php file that returns an array
* `Gettext\Extractors\Po` - Gets the strings from PO
* `Gettext\Extractors\Mo` - Gets the strings from MO

Generators
----------

Generators take a `Gettext\Translations` instance and export the data in any format.

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
* `Gettext\Generators\Jed` - Exports to json format compatible with Jed library
* `Gettext\Generators\PhpArray` - Exports to php code that returns an array with all values

HOW TO USE?
===========

First, lets scan a Po file:

```php
//Include the autoloader if you don't use composer or PSR-4 loader
include('myLibsFolder/Gettext/autoloader.php');

use Gettext\Extractors\Po as PoExtractor;

$translations = PoExtractor::fromFile('my-file.po');
```

Now, we can edit some translations:

```php
// find($context, $original, $plural)
$translation = $translations->find(null, 'apples');

if ($translation) {
	$translation->setTranslation('Mazáns');
}
```

And use the Generators to export this entry to other formats like php array or json file (for use with Jed library: http://slexaxton.github.com/Jed/):

```php
use Gettext\Generators\PhpArray as PhpArrayGenerator;
use Gettext\Generators\Jed as JedGenerator;

PhpArrayGenerator::toFile($translations, 'locale.php');
JedGenerator::toFile($translations, 'locale.json');
```

A simpler and easier way to extract/generate translations is using the magic methods:

```php
use Gettext\Translations;

$locales = Translations::fromPoFile('locale.po');
//This is the same than Gettext\Extractors\Po::fromFile('locale.po')

$locales->toMoFile('locale.mo');
//This is the same than Gettext\Generators\Mo::toFile('locale.mo')

//or to export into a string:
var_dump($locales->toMoString());
```

TRANSLATOR
==========

There is the class `Gettext\Translator` to implement some simple gettext functions in php without install the native gettext extension in php. We only have to save the translations using the `Gettext\Generators\PhpArray` generator.

```php
use Gettext\Translator;

//Create a new instance of the translator
$t = new Translator();

//Load some translations from php files
$t->loadTranslations('locales.php');

//Now you can use it in your templates
echo $t->gettext('apples'); //Returns Mazás
```

Use the translator functions, a short version of Gettext\Translator for more confort:

```php
//First set the translator instance as current translator:
__currentTranslator($t);

echo __('apples'); //Returns Mazás

__e('apples'); //echoes Mazás
```

Using these short functions, you can scan the php files to find your gettext values inside the php code:

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
$translations->toPoFile('locales.gl.po');

//And exports again the translations to .json file
$translations->toJedFile('locales.gl.json');
```

And you can use the translations exported to json in javascript with the Jed library (http://slexaxton.github.com/Jed/)

```javascript
$.getJSON('locales.gl.json', function (locale) {
	i18n = new Jed({
		locale_data: locale
	});

	alert(i18n.gettext('Hello world')); //alerts "Ola mundo"
});
```

Merge translations
------------------

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


Contributors
============

* oscarotero (Creator and maintainer)
* esnoeijs (Contributed with the plural parser)
* leom (Contributed with some Jed fixes)

TO-DO
=====

* Working with domains
