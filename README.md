Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com>

GNU Affero GPL version 3. http://www.gnu.org/licenses/agpl-3.0.html

Gettext is a PHP library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

Features:

* Written in php 5.3
* Extensible with plugins

Contains the following classes:

* Gettext\Translation - Contains a translation definition
* Gettext\Entries - A translations collection
* Gettext\Translator - A gettext implementation for PHP

Extractors
----------

The extrators are classes that extract the gettext values from any source and return a Gettext\Entries instance with them.

* Gettext\Extractors\PhpCode - Scan a php file looking for all gettext functions to collect their strings
* Gettext\Extractors\JsCode - Scan a javascript file looking for all gettext functions to collect their strings
* Gettext\Extractors\PhpArray - Gets the strings from a php file that returns an array of values (complement of PhpArray generator)
* Gettext\Extractors\Po - Gets the strings from PO files
* Gettext\Extractors\Mo - Gets the strings from MO files

Generators
----------

Generators take a Gettext\Entries instance and export the data in any of the following format.

* Gettext\Generators\Mo - Generate a Mo file
* Gettext\Generators\Po - Generate a Po file
* Gettext\Generators\Jed - Generate a json file compatible with Jed library
* Gettext\Generators\PhpArray - Generate a Php file that returns an array with all values

HOW TO USE?
===========

First, lets scan a Po file:

```php
//Include the autoloader if you don't use composer or PSR-0 loader
include('myLibsFolder/Gettext/autoloader.php');

use Gettext\Extractors\Po as PoExtractor;

$translations = PoExtractor::extract('my-file.po');
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

PhpArrayGenerator::generateFile($translations, 'locate.php');
JedGenerator::generateFile($translations, 'locate.json');
```

Now we have the location files created, so they can be loaded in our app. For example, we can use the Gettext\Translator to work with translations exported to php array:

```php
use Gettext\Translator;

$t = new Translator();

$t->loadTranslations('locate.php');

echo $t->gettext('apples'); //Returns Mazás
```

Or use the translator functions, a short version of Gettext\Translator for more confort:

```php
//First set the translator instance as current translator:
__currentTranslator($t);

echo __('apples'); //Returns Mazás

__e('apples'); //echo Mazás
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
```
$entries = Gettext\Extractors\PhpCode::extract('index.php');

//Search for the value
$translation = $entries->find(null, 'Hello world');

//Translate to other language
$translation->setTranslation('Ola mundo');

//Exports the entries to .po file (for example)
Gettext\Generators\Po::generateFile($entries, 'index.gl.po');

//And exports again the entries to .json file
Gettext\Generators\Po::generateFile($entries, 'index.gl.json');
```

And you can use the translations exported to json in javascript with the Jed library (http://slexaxton.github.com/Jed/)

```javascript
$.getJSON('index.gl.json', function (locale) {
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
//Create a new Entries instances with our translations.

$entries1 = Gettext\Extractors\Po::extract('my-file1.po');
$entries2 = Gettext\Extractors\Po::extract('my-file2.po');

//Merge one inside other:
$entries1->mergeWith($entries2);

//Now entries1 has all values
```

The second argument of `mergeWith` defines how the merge will be done. You can pass one or various of the following predefined constants:

* MERGE_ADD: Adds the translations from entries2 to entries1 if they not exists
* MERGE_REMOVE: Removes the translations in entries1 if they are not in entries2
* MERGE_HEADERS: Merges the headers from entries2 to entries 1
* MERGE_REFERENCES: Merges the references from entries2 to entries1
* MERGE_COMMENTS: Merges the comments from entries2 to entries1

Example:

```php
use Gettext\Entries;

//Scan the php code to find the latest gettext entries
$entries = Gettext\Extractors\PhpCode::extract('my-templates.php');

//Get the translations of the code that are stored in a po file
$poEntries = Gettext\Extractors\Po::extract('locale.po');

//Apply the translations from the po file to the entries, and merges header and comments but not references and without add or remove entries:
$entries->mergeWith($poEntries, Entries::MERGE_HEADERS | Entries::MERGE_COMMENTS);

//Now save a po file with the result
Gettext\Generators\Po::generateFile($entries, 'locale.po');
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
