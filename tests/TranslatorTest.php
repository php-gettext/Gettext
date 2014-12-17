<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class TranslatorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $t = new Gettext\Translator();
        $t->loadTranslations(Gettext\Translations::fromPoFile(__DIR__.'/files/po.po'));

        $this->assertEquals('Cijeo broj', $t->gettext('Integer'));
        $this->assertEquals('Ovo polje ne može biti prazno.', $t->gettext('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', $t->gettext('Value %sr is not a valid choice.'));
    }

    public function testFunctions()
    {
        //Extract translations
        $t = new Gettext\Translator();
        $t->loadTranslations(Gettext\Translations::fromPoFile(__DIR__.'/files/po.po'));

        Gettext\Translator::initGettextFunctions($t);
        
        $this->assertEquals('Cijeo broj', __('Integer'));
        $this->assertEquals('Ovo polje ne može biti prazno.', __('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', __('Value %sr is not a valid choice.'));
        $this->assertEquals('Value hellor is not a valid choice.', __('Value %sr is not a valid choice.', 'hello'));
    }
}
