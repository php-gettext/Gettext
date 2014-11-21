<?php
include_once dirname(__DIR__).'/src/autoloader.php';

if (!function_exists('n__')) {
    require_once dirname(__DIR__).'/src/translator_functions.php';
}

class MagicMethodsTest extends PHPUnit_Framework_TestCase
{
    public function testCreator()
    {
        //Extract translations
        $translations1 = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpCode-example.php');
        $translations2 = Gettext\Translations::fromPhpCodefile(__DIR__.'/files/phpCode-example.php');

        $this->assertInstanceOf('Gettext\\Translations', $translations1);
        $this->assertInstanceOf('Gettext\\Translations', $translations2);

        $this->assertEquals($translations1, $translations2);

        $result = Gettext\Generators\Po::toString($translations1);
        $this->assertEquals($result, $translations2->toPoString());
    }
}
