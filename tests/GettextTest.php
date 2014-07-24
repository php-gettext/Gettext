<?php
include_once dirname(__DIR__).'/Gettext/autoloader.php';

if (!function_exists('n__')) {
    require_once(__DIR__ . '/../Gettext/translator_functions.php');
}

class GettextTest extends PHPUnit_Framework_TestCase
{
    public function testPhpCodeExtractor()
    {
        //Extract entries
        $entries = Gettext\Extractors\PhpCode::extract(__DIR__.'/files/phpCode-example.php');

        $this->assertInstanceOf('Gettext\\Entries', $entries);

        return $entries;
    }

    public function testPoFileExtractor()
    {
        $entries = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_plural.po');

        $this->assertInstanceOf('Gettext\\Entries', $entries);

        $pluralHeader = "nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);";
        $this->assertEquals($pluralHeader, $entries->getHeader('Plural-Forms'), "Plural form did not get extracted correctly");

        return $entries;
    }

    public function testAutomaticHeaders() 
    {
        $entries = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_multiple_headers.po');
        $language = 'bs';
        $this->assertEquals($language, $entries->getLanguage(), 'Language was not extracted correctly');

        $domain = 'testingdomain';
        $this->assertEquals($domain, $entries->getDomain(), 'Domain was not extracted correctly');

        $entries2 = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_plural.po');
        $this->assertNull($entries2->getLanguage(), 'Something erroneously set for language');
        $this->assertNull($entries2->getDomain(), 'Something erroneously set for domain');

        return $entries;
    }

    public function testSplitHeader() 
    {
        $entries = Gettext\Extractors\Po::extract(__DIR__.'/files/gettext_multiple_headers.po');
        $pluralHeader = "nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);";
        $this->assertEquals($pluralHeader, $entries->getHeader('Plural-Forms'), 'header split over 2 lines not extracted correctly');
        return $entries;
    }

    /**
     * @depends testPhpCodeExtractor
     */
    public function testEntries($entries)
    {
        //Find by text
        $translation = $entries->find(null, 'text 1');

        $this->assertInstanceOf('Gettext\\Translation', $translation);

        //Find by translation object
        $translation2 = $entries->find($translation);

        $this->assertEquals($translation, $translation2);

        //Insert a new translation
        $entries->insert('my context', 'comment', 'comments');

        $commentTranslation = $entries->find('my context', 'comment', 'comments');

        $this->assertInstanceOf('Gettext\\Translation', $commentTranslation);

        $this->assertEquals('comment', $commentTranslation->getOriginal());
        $this->assertEquals('', $commentTranslation->getTranslation());
        $this->assertEquals('my context', $commentTranslation->getContext());
        $this->assertEquals('comments', $commentTranslation->getPlural());
        $this->assertTrue($commentTranslation->hasPlural());

        $entries->setHeader('POT-Creation-Date', '2012-08-07 13:03+0100');
        $this->assertEquals('2012-08-07 13:03+0100', $entries->getHeader('POT-Creation-Date'));

        return $entries;
    }

    /**
     * @depends testEntries
     */
    public function testTranslation($entries)
    {
        $translation = $entries->find(null, 'text 1');

        $this->assertEquals('text 1', $translation->getOriginal());
        $this->assertEquals('', $translation->getTranslation());
        $this->assertEquals('', $translation->getContext());
        $this->assertEquals('', $translation->getPlural());
        $this->assertFalse($translation->hasPlural());

        //References
        $references = $translation->getReferences();
        $this->assertCount(1, $references);
        $this->assertTrue($translation->hasReferences());

        list($filename, $line) = $references[0];

        $this->assertEquals(2, $line);
        $this->assertEquals(__DIR__.'/files/phpCode-example.php', $filename);

        $translation->wipeReferences();
        $this->assertCount(0, $translation->getReferences());

        //Comments
        $this->assertFalse($translation->hasComments());

        $translation->addComment('This is a comment');

        $this->assertTrue($translation->hasComments());
        $this->assertCount(1, $translation->getComments());

        $comments = $translation->getComments();
        $this->assertEquals('This is a comment', $comments[0]);

        //Plurals
        $this->assertFalse($translation->hasPlural());

        $translation->setPlural('texts 1');
        $this->assertTrue($translation->hasPlural());

        $this->assertTrue($translation->is('', 'text 1', 'texts 1'));

        $translation->setPluralTranslation('textos 1');

        $this->assertCount(1, $translation->getPluralTranslation());
        $this->assertEquals('textos 1', $translation->getPluralTranslation(0));

        return $entries;
    }

    /**
     * @depends testTranslation
     */
    public function testPhpArrayGenerator($entries)
    {
        //Export to a file
        $filename = __DIR__.'/files/tmp-phparray.php';

        $result = Gettext\Generators\PhpArray::generateFile($entries, $filename);

        $this->assertTrue($result);
        $this->assertTrue(is_file($filename));

        //Load the data as an array
        $array = include $filename;

        $this->assertTrue(is_array($array));
        $this->assertArrayHasKey('messages', $array);

        //Load the data as entries object
        $entries2 = Gettext\Extractors\PhpArray::extract($filename);

        //Compare the length of the translations in the array an in the entries (the array always has one more message)
        $this->assertEquals(count($array['messages']) - 1, count($entries2));

        unlink($filename);
    }

    /**
     * @depends testPoFileExtractor
     */
    public function testMultiPlural($entries)
    {
        $translationArray = \Gettext\Generators\PhpArray::generate($entries);
        $translator = new \Gettext\Translator;
        $translator->loadTranslationsArray($translationArray);

        //Set the current translator before execute the functions
        __currentTranslator($translator);

        /**
         * Test that nplural=3 plural translation check comes up with the correct translation key.
         */
        $this->assertEquals('1 plik',      n__ ("one file", "multiple files", 1), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', n__ ("one file", "multiple files", 2), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', n__ ("one file", "multiple files", 3), "plural calculation result bad");
        $this->assertEquals('2,3,4 pliki', n__ ("one file", "multiple files", 4), "plural calculation result bad");
        $this->assertEquals('5-21 plików', n__ ("one file", "multiple files", 5), "plural calculation result bad");
        $this->assertEquals('5-21 plików', n__ ("one file", "multiple files", 6), "plural calculation result bad");

        /**
         * Test that when less then the nplural translations are available it still works.
         */
        $this->assertEquals('1', n__ ("one", "more", 1), "non-plural fallback failed");
        $this->assertEquals('*', n__ ("one", "more", 2), "non-plural fallback failed");
        $this->assertEquals('*', n__ ("one", "more", 3), "non-plural fallback failed");

        /**
         * Test that non-plural translations the fallback still works.
         */
        $this->assertEquals('more', n__ ("single", "more", 3), "non-plural fallback failed");

    }
}
