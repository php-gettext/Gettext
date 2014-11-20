<?php
include_once dirname(__DIR__).'/src/autoloader.php';

if (!function_exists('n__')) {
    require_once(dirname(__DIR__).'/src/translator_functions.php');
}

class JavascriptTest extends PHPUnit_Framework_TestCase
{
    public function testScript() {
        $code = file_get_contents(__DIR__.'/files/script.js');
        $jsFunctionsScanner = new Gettext\Utils\JsFunctionsScanner($code);

        var_dump($jsFunctionsScanner->getFunctions());
    }
}
