Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)

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
$translation = $translations->find(null, 'apples');

if ($translation) {
	$translation->setTranslation('Mazáns');
}
```

And export to a php file and a json file for use with Jed library (http://slexaxton.github.com/Jed/:

```php
use Gettext\Generators\PhpArray as PhpArrayGenerator;
use Gettext\Generators\Jed as JedGenerator;

PhpArrayGenerator::generateFile($translations, 'locate.php');
JedGenerator::generateFile($translations, 'locate.json');
```

Now we can use this translations into our code:

```php
use Gettext\Translator as Gt;

Gt::loadTranslations('locate.php');

echo Gt::gettext('apples'); //Returns Mazás
```

You can use the translator functions, a short version of Gettext\Translator for more confort:

```php
echo __('apples'); //Returns Mazás

__e('apples'); //echo Mazás
```

And you can use the same translations in javascript with the Jed library (http://slexaxton.github.com/Jed/)

```javascript
$.getJSON('locate.json', function (locale) {
	i18n = new Jed({
		locale_data: locale
	});

	alert(i18n.gettext('apples')); //alerts "Mazás"
});
```


TO-DO
=====

* Custom plural parser
* Working with domains
