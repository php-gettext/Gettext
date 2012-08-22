<?php
include('Loader.php');

Fol\Loader::setLibrariesPath(dirname(__DIR__));
Fol\Loader::register();

//$translations = Gettext\Extractors\File::extract('gettext.html.php');
$translations = Gettext\Extractors\Po::extract('gettext.po');
//$translations = Gettext\Extractors\Mo::extract('gettext.mo');

header('Content-Type: text/plain; charset=UTF-8');

//echo(Gettext\Generators\Mo::generate($translations));
echo(Gettext\Generators\Po::generate($translations));
?>