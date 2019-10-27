Gettext
=======

[![Build Status](https://travis-ci.org/oscarotero/Gettext.png?branch=master)](https://travis-ci.org/oscarotero/Gettext)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/oscarotero/Gettext/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/oscarotero/Gettext/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gettext/gettext/v/stable.svg)](https://packagist.org/packages/gettext/gettext)
[![Total Downloads](https://poser.pugx.org/gettext/gettext/downloads.svg)](https://packagist.org/packages/gettext/gettext)
[![Monthly Downloads](https://poser.pugx.org/gettext/gettext/d/monthly.png)](https://packagist.org/packages/gettext/gettext)
[![License](https://poser.pugx.org/gettext/gettext/license.svg)](https://packagist.org/packages/gettext/gettext)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47/big.png)](https://insight.sensiolabs.com/projects/496dc2a6-43be-4046-a283-f8370239dd47)

Created by Oscar Otero <http://oscarotero.com> <oom@oscarotero.com> (MIT License)

Gettext is a PHP (^7.2) library to import/export/edit gettext from PO, MO, PHP, JS files, etc.

## Installation

With composer:

```
composer require gettext/gettext
```

## Classes and functions

This package contains the following classes:

* `Gettext\Translation` - A translation definition
* `Gettext\Translations` - A collection of translations (under the same domain)
* `Gettext\Scanner\*` - Scan files to extract translations (php, js, twig templates, ...)
* `Gettext\Loader\*` - Load translations from different formats (po, mo, json, ...)
* `Gettext\Generator\*` - Export translations to various formats (po, mo, json, ...)
* `Gettext\Translator` - To use the translations in your php templates instead the [gettext extension](http://php.net/gettext)
* `Gettext\GettextTranslator` - To use the [gettext extension](http://php.net/gettext)

## Usage example

```php
use Gettext\Loader\PoLoader;
use Gettext\Generator\MoGenerator;

//import from a .po file:
$loader = new PoLoader();
$translations = $loader->loadFile('locales/gl.po');

//edit some translations:
$translation = $translations->find(null, 'apple');

if ($translation) {
    $translation->setTranslation('Mazá');
}

//export to a .mo file:
$generator = new MoGenerator();
$generator->generateFile($translations, 'Locale/gl/LC_MESSAGES/messages.mo');
```

## Translation

The `Gettext\Translation` class stores all information about a translation: the original text, the translated text, source references, comments, etc.

```php
use Gettext\Translation;

$translation = Translation::create('comments', 'One comment', '%s comments');

$translation->translate('Un comentario');
$translation->translatePlural('%s comentarios');

$translation->getReferences()->add('templates/comments/comment.php', 34);
$translation->getComments()->add('To display the amount of comments in a post');

echo $translation->getContext(); // comments
echo $translation->getOriginal(); // One comment
echo $translation->getTranslation(); // Un comentario

// etc...
```

## Translations

The `Gettext\Translations` class stores a collection of translations:

```php
use Gettext\Translations;

$translations = Translations::create('my-domain');

//You can add new translations:
$translation = Translation::create('comments', 'One comment', '%s comments');
$translations->add($translation);

//Find a specific translation
$translation = $translations->find('comments', 'One comment');

//Edit headers, domain, etc
$translations->getHeaders()->add('Last-Translator', 'Oscar Otero');
$translations->setDomain('my-blog');
```

## Loaders

The loaders allows to get gettext values from any format. For example, to load a .po file:

```php
use Gettext\Loader\PoLoader;

$loader = new PoLoader();

//From a file
$translations = $loader->loadFile('locales/en.po');

//From a string
$string = file_get_contents('locales2/en.po');
$translations = $loader->loadString($string);
```

This package includes the following loaders:

- `ArrayLoader`
- `MoLoader`
- `PoLoader`

## Generators

The generators export a `Gettext\Translations` instance to any format (po, mo, array, etc).

```php
use Gettext\Loader\PoLoader;
use Gettext\Generator\MoGenerator;

//Load a PO file
$poLoader = new PoLoader();

$translations = $poLoader->loadFile('locales/en.po');

//Save to MO file
$moGenerator = new MoGenerator();

$moGenerator->generateFile($translations, 'locales/en.mo');

//Or return as a string
$content = $moGenerator->generateString($translations);
file_put_contents('locales/en.mo', $content);
```

This package includes the following generators:

- `ArrayGenerator`
- `MoGenerator`
- `PoGenerator`


## Scanners

Scanners allow to search and extract new gettext entries from different sources like php files, twig templates, blade templates, etc. Unlike loaders, scanners allows to extract gettext entries with different domains:

```php
use Gettext\Scanner\PhpScanner;
use Gettext\Translations;

//Create a new scanner, adding a translation for each domain we want to get:
$phpScanner = new PhpScanner(
    Translations::create('domain1'),
    Translations::create('domain2'),
    Translations::create('domain3')
);

//Scan files
foreach (glob('*.php') as $file) {
    $phpScanner->scanFile($file);
}

//Get the translations
list($domain1, $domain2, $domain3) = $phpScanner->getTranslations();
```

## Merging translations

You will want to update or merge translations. The function `mergeWith` create a new `Translations` instance with other translations merged:

```php
$translations3 = $translations1->mergeWith($translations2);
```

But sometimes this is not enough, and this is why we have merging options, allowing to configure how two translations will be merged. These options are defined as constants in the `Gettext\Merge` class, and are the following:

Constant | Description
--------- | -----------
`Merge::TRANSLATIONS_OURS` | Use only the translations present in `$translations1`
`Merge::TRANSLATIONS_THEIRS` | Use only the translations present in `$translations2`
`Merge::TRANSLATION_OVERRIDE` | Override the translation and plural translations with the value of `$translation2`
`Merge::HEADERS_OURS` | Use only the headers of `$translations1`
`Merge::HEADERS_REMOVE` | Use only the headers of `$translations2`
`Merge::HEADERS_OVERRIDE` | Overrides the headers with the values of `$translations2`
`Merge::COMMENTS_OURS` | Use only the comments of `$translation1`
`Merge::COMMENTS_THEIRS` | Use only the comments of `$translation2`
`Merge::EXTRACTED_COMMENTS_OURS` | Use only the extracted comments of `$translation1`
`Merge::EXTRACTED_COMMENTS_THEIRS` | Use only the extracted comments of `$translation2`
`Merge::FLAGS_OURS` | Use only the flags of `$translation1`
`Merge::FLAGS_THEIRS` | Use only the flags of `$translation2`
`Merge::REFERENCES_OURS` | Use only the references of `$translation1`
`Merge::REFERENCES_THEIRS` | Use only the references of `$translation2`

Use the second argument to configure the merging strategy:

```php
$strategy = Merge::TRANSLATIONS_OURS | Merge::HEADERS_OURS;

$translations3 = $translations1->mergeWith($translations2, $strategy);
```

There are some typical scenarios, one of the most common:

- Scan php templates searching for entries to translate
- Complete these entries with the translations stored in a .po file
- You may want to add new entries to the .po file
- And also remove those entries present in the .po file but not in the templates (because they were removed)
- But you want to update some translations with new references and extracted comments
- And keep the translations, comments and flags defined in .po file

For this scenario, you can use the option `Merge::SCAN_AND_LOAD` with the combination of options to fit this needs (SCAN new entries and LOAD a .po file).

```php
$newEntries = $scanner->scanFile('template.php');
$previousEntries = $loader->loadFile('translations.po');

$updatedEntries = $newEntries->mergeWith($previousEntries);
```

More common scenarios may be added in a future.

## Contributors

Thanks to all [contributors](https://github.com/oscarotero/Gettext/graphs/contributors) specially to [@mlocati](https://github.com/mlocati).

## Donations

If this library is useful for you, consider to donate to the author.

[Buy me a beer :beer:](https://www.paypal.me/oscarotero)

Thanks in advance!
