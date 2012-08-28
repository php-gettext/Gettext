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
$dictionary = Gettext\Generators\Php::generate($translations);

Gettext\Translator::addTranslations($dictionary);

echo __n('%s point', '%s points', 4, 4);
?>
