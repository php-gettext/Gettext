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
        $translations = Translations::fromPhpCodeFile(static::asset('2/Input.PhpCode.php'));

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
        $translations = Translations::fromPoFile(static::asset('3/Input.Po.po'));

        $this->assertEquals('gettext generator test', $translations->getHeader('Project-Id-Version'));

        $translations->setHeader('POT-Creation-Date', '2012-08-07 13:03+0100');
        $this->assertEquals('2012-08-07 13:03+0100', $translations->getHeader('POT-Creation-Date'));
    }

    public function testMergeDefault()
    {
        $translations1 = Translations::fromPoFile(static::asset('1/Po.po'));
        $translations2 = Translations::fromPoFile(static::asset('2/Po.po'));

        $this->assertCount(Asset1Test::COUNT_TRANSLATIONS, $translations1);
        $this->assertCount(Asset2Test::COUNT_TRANSLATIONS, $translations2);

        $translations1->mergeWith($translations2);

        $this->assertCount(Asset1Test::COUNT_TRANSLATIONS + Asset2Test::COUNT_TRANSLATIONS, $translations1);
    }

    public function testAdd()
    {
        $translations = Translations::fromPoFile(static::asset('1/Po.po'));
        $translations->addFromPoFile(static::asset('2/Po.po'));

        $this->assertCount(Asset1Test::COUNT_TRANSLATIONS + Asset2Test::COUNT_TRANSLATIONS, $translations);
    }
}
