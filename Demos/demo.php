<?php
//Simple autoload function

ini_set('display_errors', 'On');

function autoload ($className) {
	$className = ltrim($className, '\\');
	$fileName  = '../';
	$namespace = '';
	
	if ($lastNsPos = strripos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}

	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if (is_file($fileName)) {
		require $fileName;
	}
}

spl_autoload_register('autoload');

include('../Gettext/translator_functions.php');

//Demo

$translations = Gettext\Extractors\JsCode::extract('javascript.js');
$translations = Gettext\Extractors\Po::extract('gettext.po');
$translation = $translations->find(null, 'Your tiles were set successfully');
var_dump($translation);
var_dump($translation->is(null, 'Your tiles were set successfully'));
die();
//$translations = Gettext\Extractors\PhpCode::extract('gettext.html.php');
//$translations = Gettext\Extractors\Mo::extract('gettext.mo');

header('Content-Type: text/plain; charset=UTF-8');

//echo(Gettext\Generators\Mo::generate($translations));
Gettext\Generators\PhpArray::generateFile($translations, 'gettext.php');
Gettext\Generators\Jed::generateFile($translations, 'gettext.json');

Gettext\Translator::loadTranslations('gettext.php');

echo __n('%s point', '%s points', 4, 4);
?>
