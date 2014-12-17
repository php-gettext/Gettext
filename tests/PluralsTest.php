<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class PluralsTest extends PHPUnit_Framework_TestCase
{
    public function testMultiPlural()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $translator = new \Gettext\Translator();
        $translator->loadTranslations($translations);

        /**
         * Test that nplural=3 plural translation check comes up with the correct translation key.
         */
        $this->assertEquals('1 plik',      $translator->ngettext("one file", "multiple files", 1), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', $translator->ngettext("one file", "multiple files", 2), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', $translator->ngettext("one file", "multiple files", 3), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', $translator->ngettext("one file", "multiple files", 4), "plural calculation result bad");
        $this->assertEquals('5-21 plików', $translator->ngettext("one file", "multiple files", 5), "plural calculation result bad");
        $this->assertEquals('5-21 plików', $translator->ngettext("one file", "multiple files", 6), "plural calculation result bad");

        /**
         * Test that when less then the nplural translations are available it still works.
         */
        $this->assertEquals('1', $translator->ngettext("one", "more", 1), "non-plural fallback failed");
        $this->assertEquals('*', $translator->ngettext("one", "more", 2), "non-plural fallback failed");
        $this->assertEquals('*', $translator->ngettext("one", "more", 3), "non-plural fallback failed");

        /**
         * Test that non-plural translations the fallback still works.
         */
        $this->assertEquals('more', $translator->ngettext("single", "more", 3), "non-plural fallback failed");
    }
}
