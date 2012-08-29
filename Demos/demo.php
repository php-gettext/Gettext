<?php
include('Loader.php');
include('../Gettext/translator_functions.php');

Fol\Loader::setLibrariesPath(dirname(__DIR__));
Fol\Loader::register();

//$translations = Gettext\Extractors\File::extract('gettext.html.php');
$translations = Gettext\Extractors\Po::extract('gettext.po');
//$translations = Gettext\Extractors\Mo::extract('gettext.mo');

header('Content-Type: text/plain; charset=UTF-8');

//echo(Gettext\Generators\Mo::generate($translations));
Gettext\Generators\Php::generateFile($translations, 'gettext.php');
Gettext\Generators\Jed::generateFile($translations, 'translation.json');

Gettext\Translator::loadTranslations('gettext.php');

echo __n('%s point', '%s points', 4, 4);
?>
