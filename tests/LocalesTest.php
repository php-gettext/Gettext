<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class LocalesTest extends PHPUnit_Framework_TestCase
{
    public function testPlurals()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');
        $t = $translations->find(null, 'one file');

        $this->assertInstanceOf('Gettext\\Translations', $translations);
        $this->assertEquals('nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', $translations->getHeader('Plural-Forms'));

        $translations->setPluralForms(2, '(n != 1)');
        $this->assertEquals('nplurals=2; plural=(n != 1);', $translations->getHeader('Plural-Forms'));
        
        $translations->setLanguage('ru');
        $this->assertEquals('nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);', $translations->getHeader('Plural-Forms'));
    }
}
