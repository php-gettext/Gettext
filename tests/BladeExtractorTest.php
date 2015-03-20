<?php

class BladeExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $translations = Gettext\Extractors\Blade::fromFile(__DIR__.'/files/code.blade.php');

        $this->assertInstanceOf('Gettext\\Translations', $translations);
        $this->assertInstanceOf('Gettext\\Translation', $translations->find('context', 'text 1 with context'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 2'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 3 (with parenthesis)'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 4 "with double quotes"'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 5 \'with escaped single quotes\''));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 6'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 7 (with parenthesis)'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 8 "with escaped double quotes"'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'text 9 \'with single quotes\''));
    }
}
