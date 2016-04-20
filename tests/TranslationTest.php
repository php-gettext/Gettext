<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Translations;

class TranslationTest extends AbstractTest
{
    public function testReferences()
    {
        $translations = Translations::fromPhpCodeFile(static::asset('two.raw.php'));
        $translation = $translations->find(null, 'text 10 with plural');

        $this->assertInstanceOf('Gettext\\Translation', $translation);

        $references = $translation->getReferences();

        $this->assertCount(1, $references);
        $this->assertTrue($translation->hasReferences());
        $this->assertEquals(static::asset('two.raw.php'), $references[0][0]);
        $this->assertEquals(19, $references[0][1]);

        $translation->deleteReferences();
        $this->assertCount(0, $translation->getReferences());
    }

    public function testPlurals()
    {
        $translations = Translations::fromPhpCodeFile(static::asset('two.raw.php'));
        $translation = $translations->find(null, 'text 10 with plural');

        $this->assertTrue($translation->hasPlural());
        $this->assertTrue($translation->is('', 'text 10 with plural'));

        $translation = $translations->find(null, 'text 2');

        $this->assertFalse($translation->hasPlural());

        $translation->setPluralTranslations(['texts 2']);

        $pluralTranslations = $translation->getPluralTranslations();
        $this->assertCount(1, $pluralTranslations);
        $this->assertEquals('texts 2', $pluralTranslations[0]);
    }

    public function testMerge()
    {
        $one = new Translation(null, '1 child');
        $two = new Translation(null, '1 child');
        $two->setTranslation('1 fillo');

        $one->mergeWith($two);

        $this->assertEquals('1 child', $one->getOriginal());
        $this->assertEquals('1 fillo', $one->getTranslation());
    }
}
