<?php

class JsCodeExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $translations = Gettext\Extractors\JsCode::fromFile(__DIR__.'/files/jscode.js');

        $this->assertInstanceOf('Gettext\\Translations', $translations);

        $this->assertCount(7, $translations);

        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'Value with simple quotes'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'Value with double quotes'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'Other value with double quotes'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'function inside function'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'I can\'t get response.'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'Please, try with other interface type.'));
        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, 'I can\'t get response. Please, try with other interface type.'));
    }
}
