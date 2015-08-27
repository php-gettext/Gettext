<?php

class TranslationsTest extends PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        //Extract translations
        $translations = Gettext\Extractors\PhpCode::fromFile(__DIR__.'/files/phpcode.php');

        //Find by original
        $translation = $translations->find(null, 'text 2');

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals('text 2', $translation->getOriginal());

        //Find by context
        $translation = $translations->find('context', 'text 1 with context');

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals('text 1 with context', $translation->getOriginal());
        $this->assertEquals('context', $translation->getContext());

        //Find by plural
        $translation = $translations->find(null, 'text 10 with plural');

        $this->assertInstanceOf('Gettext\\Translation', $translation);
        $this->assertEquals('text 10 with plural', $translation->getOriginal());
        $this->assertEquals('The plural form', $translation->getPlural());
        $this->assertTrue($translation->hasPlural());

        //No results
        $translation = $translations->find(null, 'text 1 with context');
        $this->assertFalse($translation);

        $translation = $translations->find('no-valid-context', 'text 1 with context');
        $this->assertFalse($translation);

        $translation = $translations->find('context', 'text 2');
        $this->assertFalse($translation);

        $translation = $translations->find(null, 'no valid text 2');
        $this->assertFalse($translation);
    }

    public function testGettersSetters()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $this->assertEquals('gettext generator test', $translations->getHeader('Project-Id-Version'));

        $translations->setHeader('POT-Creation-Date', '2012-08-07 13:03+0100');
        $this->assertEquals('2012-08-07 13:03+0100', $translations->getHeader('POT-Creation-Date'));
    }

    public function testMergeDefault()
    {
        $translations1 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');
        $translations2 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $this->assertCount(11, $translations1);
        $this->assertCount(3, $translations2);

        $translations1->mergeWith($translations2);

        $this->assertCount(14, $translations1);
    }

    public function testAdd()
    {
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');
        $translations->addFromPoFile(__DIR__.'/files/plurals.po');

        $this->assertCount(14, $translations);
    }

    public function testMergeAddRemove()
    {
        $translations1 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');
        $translations2 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $translations1->mergeWith($translations2, Gettext\Translations::MERGE_REMOVE |  Gettext\Translations::MERGE_ADD);

        $this->assertCount(3, $translations1);
    }

    public function testMergeRemove()
    {
        $translations1 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');
        $translations2 = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $translations1->mergeWith($translations2, Gettext\Translations::MERGE_REMOVE);

        $this->assertCount(0, $translations1);
    }

    public function testMergeOverride()
    {
        $default = <<<EOT
msgid "unit"
msgstr "Unit"
EOT;

        $override = <<<EOT
msgid "unit"
msgstr "Use this instead"
EOT;

        $translations1 = Gettext\Extractors\Po::fromString($default);
        $translations2 = Gettext\Extractors\Po::fromString($override);

        $translations1->mergeWith($translations2, Gettext\Translations::MERGE_OVERRIDE);

        $this->assertCount(1, $translations1);
        $this->assertEquals('Use this instead', $translations1->find(null, 'unit')->getTranslation());
    }

    public function testMergeReferences()
    {
        $translations1 =  new Gettext\Translations();
        $translation1 = new Gettext\Translation(null, 'apple');
        $translation1->addReference($comment = 'templates/hello.php', $line = 34);
        $translations1[] = $translation1;

        $this->assertTrue($translation1->hasReferences());
        $this->assertCount(1, $actualRef = $translation1->getReferences());
        $expectedRef1 = array($comment, $line);
        $this->assertEquals($expectedRef1, current($actualRef));

        $translation2 = new Gettext\Translation(null, 'apple');
        $translation2->addReference($comment = 'templates/world.php', $line = 134);
        $translations2 = new Gettext\Translations(array($translation2, new Gettext\Translation(null, 'orange')));

        $this->assertTrue($translation2->hasReferences());
        $this->assertCount(1, $actualRef = $translation2->getReferences());
        $expectedRef2 = array($comment, $line);
        $this->assertEquals($expectedRef2, current($actualRef));

        //merge with references
        $translations1->mergeWith($translations2, Gettext\Translations::MERGE_ADD | Gettext\Translations::MERGE_REFERENCES);

        //translation merged (orange)
        $this->assertInstanceOf('Gettext\\Translation', $translations1->find(null, 'orange'));

        //references merged (apple)
        $this->assertInstanceOf('Gettext\\Translation', $translations1->find(null, 'apple'));
        $this->assertTrue($translation1->hasReferences());
        $this->assertCount(2, $actualRef = $translation1->getReferences());
        $this->assertEquals(array($expectedRef1, $expectedRef2), $actualRef);
    }
}
