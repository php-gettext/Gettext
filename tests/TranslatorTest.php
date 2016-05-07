<?php

namespace Gettext\Tests;

use Gettext\Translations;
use Gettext\Translator;

class TranslatorTest extends AbstractTest
{
    public function testOne()
    {
        $t = new Translator();

        $t->loadTranslations(Translations::fromPoFile(static::asset('one.po')));
        $t->loadTranslations(Translations::fromPoFile(static::asset('three.po')));

        $this->assertEquals('test', $t->gettext('single'));
        $this->assertEquals('test', $t->dgettext('', 'single'));

        $this->assertEquals('Cijeo broj', $t->dgettext('testingdomain', 'Integer'));

        $t->defaultDomain('testingdomain');

        $this->assertEquals('Ovo polje ne može biti prazno.', $t->gettext('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', $t->gettext('Value %sr is not a valid choice.'));
    }

    public function testFunctions()
    {
        $t = new Translator();
        $t->loadTranslations(Translations::fromPoFile(static::asset('three.po')));

        $t->register();

        $this->assertEquals('Cijeo broj', __('Integer'));
        $this->assertEquals('Ovo polje ne može biti prazno.', __('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', __('Value %sr is not a valid choice.'));
        $this->assertEquals('Value hellor is not a valid choice.', __('Value %sr is not a valid choice.', 'hello'));
    }

    public function testPlural()
    {
        $t = new Translator();
        $t->loadTranslations(Translations::fromPoFile(static::asset('one.po')));

        // Test that nplural=3 plural translation check comes up with the correct translation key.
        $this->assertEquals('1 plik',      $t->ngettext('one file', 'multiple files', 1));
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 2));
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 3));
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 4));
        $this->assertEquals('5-21 plików', $t->ngettext('one file', 'multiple files', 5));
        $this->assertEquals('5-21 plików', $t->ngettext('one file', 'multiple files', 6));

        // Test that when less then the nplural translations are available it still works.
        $this->assertEquals('1', $t->ngettext('one', 'more', 1));
        $this->assertEquals('*', $t->ngettext('one', 'more', 2));
        $this->assertEquals('*', $t->ngettext('one', 'more', 3));

        // Test that non-plural translations the fallback still works.
        $this->assertEquals('more', $t->ngettext('single', 'more', 3));
    }

    public function testNonLoadedTranslations()
    {
        $t = new Translator();

        $this->assertEquals('hello', $t->gettext('hello'));
        $this->assertEquals('worlds', $t->ngettext('world', 'worlds', 0));
        $this->assertEquals('world', $t->ngettext('world', 'worlds', 1));
        $this->assertEquals('worlds', $t->ngettext('world', 'worlds', 2));
    }

    public function testHeaders()
    {
        $po = (new Translator())->loadTranslations(Translations::fromPoFile(static::asset('one.po')));
        $mo = (new Translator())->loadTranslations(Translations::fromMoFile(static::asset('one.mo')));
        $array = (new Translator())->loadTranslations(Translations::fromPhpArrayFile(static::asset('one.php')));

        $this->assertNotEmpty($po->gettext(''));
        $this->assertEquals($po->gettext(''), $mo->gettext(''));
        $this->assertEquals($po->gettext(''), $array->gettext(''));
    }
}
