<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Translations;

class TranslationsTest extends AbstractTest
{
    public function testClone()
    {
        $translations = new Translations();
        $translation = new Translation('', 'Test');
        $translations->append($translation);

        $clonedTranslation = clone $translation;
        $this->assertNotSame($translation, $clonedTranslation);

        $clonedTranslations = clone $translations;

        $found = $translations->find($translation);
        $this->assertSame($found, $translation);

        $clonedFound = $clonedTranslations->find($translation);
        $this->assertInstanceOf('Gettext\\Translation', $clonedFound);
        $this->assertNotSame($clonedFound, $translation);
    }

    public function testFind()
    {
        $translations = Translations::fromPhpCodeFile(static::asset('two.raw.php'));

        //Find by original
        $found = $translations->find(null, 'text 2');

        $this->assertInstanceOf('Gettext\\Translation', $found);
        $this->assertEquals('text 2', $found->getOriginal());

        //Find by context
        $found = $translations->find('context', 'text 1 with context');

        $this->assertInstanceOf('Gettext\\Translation', $found);
        $this->assertEquals('text 1 with context', $found->getOriginal());
        $this->assertEquals('context', $found->getContext());

        //No results
        $found = $translations->find(null, 'text 1 with context');
        $this->assertFalse($found);

        $found = $translations->find('no-valid-context', 'text 1 with context');
        $this->assertFalse($found);

        $found = $translations->find('context', 'text 2');
        $this->assertFalse($found);

        $found = $translations->find(null, 'no valid text 2');
        $this->assertFalse($found);
    }

    public function testGettersSetters()
    {
        $translations = Translations::fromPoFile(static::asset('three.raw.po'));

        $this->assertEquals('gettext generator test', $translations->getHeader('Project-Id-Version'));

        $translations->setHeader('POT-Creation-Date', '2012-08-07 13:03+0100');
        $this->assertEquals('2012-08-07 13:03+0100', $translations->getHeader('POT-Creation-Date'));
    }

    public function testMergeDefault()
    {
        $translations1 = Translations::fromPoFile(static::asset('one.po'));
        $translations2 = Translations::fromPoFile(static::asset('two.po'));

        $this->assertCount(OneTest::COUNT_TRANSLATIONS, $translations1);
        $this->assertCount(TwoTest::COUNT_TRANSLATIONS, $translations2);

        $translations1->mergeWith($translations2);

        $this->assertCount(OneTest::COUNT_TRANSLATIONS + TwoTest::COUNT_TRANSLATIONS, $translations1);
    }

    public function testAdd()
    {
        $translations = Translations::fromPoFile(static::asset('one.po'));
        $translations->addFromPoFile(static::asset('two.po'));

        $this->assertCount(OneTest::COUNT_TRANSLATIONS + TwoTest::COUNT_TRANSLATIONS, $translations);
    }

    public function testMergeAddRemove()
    {
        $translations1 = Translations::fromPoFile(static::asset('one.po'));
        $translations2 = Translations::fromPoFile(static::asset('two.po'));

        $translations1->mergeWith($translations2, Translations::MERGE_REMOVE | Translations::MERGE_ADD);

        $this->assertCount(TwoTest::COUNT_TRANSLATIONS, $translations1);
    }

    public function testMergeRemove()
    {
        $translations1 = Translations::fromPoFile(static::asset('one.po'));
        $translations2 = Translations::fromPoFile(static::asset('two.po'));

        $translations1->mergeWith($translations2, Translations::MERGE_REMOVE);

        $this->assertCount(0, $translations1);
    }

    public function testMergeOverride()
    {
        $translations1 = Translations::fromPoFile(static::asset('one.po'));
        $translations2 = Translations::fromPoFile(static::asset('one.po'));

        $found = $translations2->find(null, 'single');
        $found->setTranslation('Use this instead');

        $translations1->mergeWith($translations2, Translations::MERGE_OVERRIDE);

        $this->assertCount(OneTest::COUNT_TRANSLATIONS, $translations1);
        $this->assertEquals('Use this instead', $translations1->find(null, 'single')->getTranslation());
    }

    public function testMergeReferences()
    {
        $translations1 = new Translations();
        $translation1 = new Translation(null, 'apple');
        $translation1->addReference($comment = 'templates/hello.php', $line = 34);
        $translations1[] = $translation1;

        $this->assertTrue($translation1->hasReferences());
        $this->assertCount(1, $actualRef = $translation1->getReferences());
        $expectedRef1 = array($comment, $line);
        $this->assertEquals($expectedRef1, current($actualRef));

        $translation2 = new Translation(null, 'apple');
        $translation2->addReference($comment = 'templates/world.php', $line = 134);
        $translations2 = new Translations(array($translation2, new Translation(null, 'orange')));

        $this->assertTrue($translation2->hasReferences());
        $this->assertCount(1, $actualRef = $translation2->getReferences());
        $expectedRef2 = array($comment, $line);
        $this->assertEquals($expectedRef2, current($actualRef));

        //merge with references
        $translations1->mergeWith($translations2, Translations::MERGE_ADD | Translations::MERGE_REFERENCES);

        //translation merged (orange)
        $this->assertInstanceOf('Gettext\\Translation', $translations1->find(null, 'orange'));

        //references merged (apple)
        $this->assertInstanceOf('Gettext\\Translation', $translations1->find(null, 'apple'));
        $this->assertTrue($translation1->hasReferences());
        $this->assertCount(2, $actualRef = $translation1->getReferences());
        $this->assertEquals(array($expectedRef1, $expectedRef2), $actualRef);
    }
}
