<?php

class PluralsHandlingTest extends PHPUnit_Framework_TestCase
{
    public function testPlurals()
    {
        // Let's populate a neutral Translations instance
        $pot = new Gettext\Translations();

        $singularUntranslated = new Gettext\Translation('', 'Common singular untranslated');
        $this->assertNull($singularUntranslated->getTranslationCount());
        $pot->append($singularUntranslated);

        $singularTranslated = new Gettext\Translation('', 'Common singular translated');
        $singularTranslated->setTranslation('Common singular translation');
        $this->assertNull($singularTranslated->getTranslationCount());
        $pot->append($singularTranslated);

        $pluralUntranslated = new Gettext\Translation('', 'Common plural untranslated', 'Common plurals untranslated');
        $pot->append($pluralUntranslated);

        $pluralTranslated = new Gettext\Translation('', 'Common plural translated', 'Common plurals translated');
        $pluralTranslated->setTranslation('Common plural translation');
        $pluralTranslated->setPluralTranslation('Common plural translations');
        $pot->append($pluralTranslated);

        // Japanese
        $ja = clone $pot;
        $this->assertTrue($ja->setLanguage('ja'));
        $t = $ja->find($pluralTranslated);
        $this->assertSame(1, $t->getTranslationCount());

        // English
        $en = clone $pot;
        $this->assertTrue($en->setLanguage('en'));
        $t = $en->find($pluralTranslated);
        $this->assertSame(2, $t->getTranslationCount());

        // Russian
        $ru = clone $pot;
        $this->assertTrue($ru->setLanguage('ru'));
        $t = $ru->find($pluralTranslated);
        $this->assertSame(3, $t->getTranslationCount());

        // Merge tests
        $ja2 = clone $ja;
        $ja2->mergeWith($pot);
        $t = $ja2->find($pluralTranslated);
        $this->assertSame(1, $t->getTranslationCount());
        $ja2 = clone $pot;
        $ja2->mergeWith($ja);
        $t = $ja2->find($pluralTranslated);
        $this->assertSame(1, $t->getTranslationCount());
    }
}
