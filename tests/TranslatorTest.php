<?php

class TranslatorTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        //Extract translations
        $t = new Gettext\Translator();
        $t->loadTranslations(Gettext\Translations::fromPoFile(__DIR__.'/files/po.po'));
        $t->loadTranslations(Gettext\Translations::fromPoFile(__DIR__.'/files/plurals.po'));

        $this->assertEquals('Cijeo broj', $t->gettext('Integer'));
        $this->assertEquals('Ovo polje ne može biti prazno.', $t->gettext('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', $t->gettext('Value %sr is not a valid choice.'));

        //domains
        $this->assertEquals('single', $t->gettext('single'));
        $this->assertEquals('test', $t->dgettext('messages', 'single'));
    }

    public function testFunctions()
    {
        //Extract translations
        $t = new Gettext\Translator();
        $t->loadTranslations(Gettext\Translations::fromPoFile(__DIR__.'/files/po.po'));

        $t->register();

        $this->assertEquals('Cijeo broj', __('Integer'));
        $this->assertEquals('Ovo polje ne može biti prazno.', __('This field cannot be blank.'));
        $this->assertEquals('Value %sr is not a valid choice.', __('Value %sr is not a valid choice.'));
        $this->assertEquals('Value hellor is not a valid choice.', __('Value %sr is not a valid choice.', 'hello'));
    }

    public function testPlural()
    {
        $t = new Gettext\Translator();
        $t->loadTranslations(Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po'));

        /*
         * Test that nplural=3 plural translation check comes up with the correct translation key.
         */
        $this->assertEquals('1 plik',      $t->ngettext('one file', 'multiple files', 1), 'plural calculation result bad');
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 2), 'plural calculation result bad');
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 3), 'plural calculation result bad');
        $this->assertEquals('2,3,4 pliki', $t->ngettext('one file', 'multiple files', 4), 'plural calculation result bad');
        $this->assertEquals('5-21 plików', $t->ngettext('one file', 'multiple files', 5), 'plural calculation result bad');
        $this->assertEquals('5-21 plików', $t->ngettext('one file', 'multiple files', 6), 'plural calculation result bad');

        /*
         * Test that when less then the nplural translations are available it still works.
         */
        $this->assertEquals('1', $t->ngettext('one', 'more', 1), 'non-plural fallback failed');
        $this->assertEquals('*', $t->ngettext('one', 'more', 2), 'non-plural fallback failed');
        $this->assertEquals('*', $t->ngettext('one', 'more', 3), 'non-plural fallback failed');

        /*
         * Test that non-plural translations the fallback still works.
         */
        $this->assertEquals('more', $t->ngettext('single', 'more', 3), 'non-plural fallback failed');
    }

    public function testNonLoadedTranslations()
    {
        $t = new Gettext\Translator();

        $this->assertEquals('hello', $t->gettext('hello'));
        $this->assertEquals('worlds', $t->ngettext('world', 'worlds', 0));
        $this->assertEquals('world', $t->ngettext('world', 'worlds', 1));
        $this->assertEquals('worlds', $t->ngettext('world', 'worlds', 2));
    }
}
