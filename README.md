Gettext
=======

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com>

GNU Affero GPL version 3. http://www.gnu.org/licenses/agpl-3.0.html

Gettext is a PHP library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

Features:

* Written in php 5.3
* Extensible with plugins
* Uses the PSR-0 autoloader standard

Contains the following classes:

* Gettext\Translation - Contains a translation definition
* Gettext\Entries - A translations collection
* Gettext\Translator - A gettext implementation for PHP

Extractors
----------

The extrators are static classes that extract the gettext values from any source and return a Gettext\Entries instance with them.

* Gettext\Extractors\File - Scan a file and search for __() and __e() functions to collect all gettext strings
* Gettext\Extractors\Po - Parse a PO file
* Gettext\Extractors\Mo - Parse a MO file

Generators
----------

Generators take a Gettext\Entries instance and export the data in any format.

* Gettext\Generators\Mo - Generate a Mo file
* Gettext\Generators\Po - Generate a Mo file
* Gettext\Generators\Php - Generate a Php file (that returns an array with all values)

HOW TO USE?
===========

First, lets scan a Po file:

```php
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
use Gettext\Generators\Php as PhpGenerator;
use Gettext\Generators\Jed as JedGenerator;

PhpGenerator::generateFile($translations, 'locate.php');
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

* Jed and Php Extractors
* Create a generic autoextractor and autogenerator
* Complete the File extractor
* Custom plural parser
