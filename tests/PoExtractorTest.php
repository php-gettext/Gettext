<?php

class PoExtractorTest extends PHPUnit_Framework_TestCase
{
    public function testNoHeadersParser()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/no-headers.po');

        $this->assertCount(5, $translations);

        $this->assertCount(10, $translations->getHeaders(), $translations->toPoString());
    }

    public function testParser()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $this->assertInstanceOf('Gettext\\Translation', $translations->find(null, '%ss must be unique for %ss %ss.'));

        $this->assertEquals($translations->find(null, 'and')->getFlags(), array('c-format'));
        $this->assertEquals($translations->find(null, 'Value %sr is not a valid choice.')->getExtractedComments(), array('This is a extracted comment'));
    }

    public function testPluralHeader()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $this->assertInstanceOf('Gettext\\Translations', $translations);

        $pluralHeader = 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);';
        $this->assertEquals($pluralHeader, $translations->getHeader('Plural-Forms'), 'Plural form did not get extracted correctly');
    }

    public function testMultilineHeader()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $this->assertInstanceOf('Gettext\\Translations', $translations);

        $pluralHeader = 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);';
        $this->assertEquals($pluralHeader, $translations->getHeader('Plural-Forms'), 'header split over 2 lines not extracted correctly');
    }

    public function testAutomaticHeaders()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $this->assertEquals('bs', $translations->getLanguage(), 'Language was not extracted correctly');
        $this->assertEquals('testingdomain', $translations->getDomain(), 'Domain was not extracted correctly');

        //Extract po with no language/domain headers
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

        $this->assertEmpty($translations->getLanguage(), 'Something erroneously set for language');
        $this->assertNull($translations->getDomain(), 'Something erroneously set for domain');
    }

    public function testReferences()
    {
        //Extract translations
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $this->assertEquals(
            $translations->find(null, 'This field cannot be null.')->getReferences(),
            array(
                array('C:/Users/Me/Documents/foo2.php', '1'),
            )
        );

        $this->assertEquals(
            $translations->find(null, 'This field cannot be blank.')->getReferences(),
            array(
                array('C:/Users/Me/Documents/foo1.php', null),
            )
        );

        $this->assertEquals(
            $translations->find(null, 'Field of type: %ss')->getReferences(),
            array(
                array('attributes/address/composer.php', '8'),
                array('attributes/address/form.php', '7'),
            )
        );
    }

    public function testMultilineTranslation()
    {
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

        $t1 = $translations->find(null, '{test1}');
        $this->assertInstanceOf('Gettext\\Translation', $t1);

        $t2 = $translations->find(null, '{test2}');
        $this->assertInstanceOf('Gettext\\Translation', $t2);

        $this->assertEquals($t1->getTranslation(), $t2->getTranslation());
    }

    public function testDuplicates()
    {
        $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/duplicate.po');

        //duplicate singular
        $t = $translations->find(null, '1 child');
        $this->assertInstanceOf('Gettext\\Translation', $t);
        $this->assertEquals('1 fillo', $t->getTranslation());

        //duplicate singular - plural
        $t = $translations->find(null, '1 comment');
        $this->assertInstanceOf('Gettext\\Translation', $t);
        $this->assertEquals('%s comments', $t->getPlural());
        $this->assertEquals('1 comentario', $t->getTranslation());
        $this->assertTrue($t->hasPlural());
        $this->assertFalse($t->hasPluralTranslation());
        $t->setPluralTranslation('% comentarios');
        $this->assertTrue($t->hasPluralTranslation());

        //duplicate plural - singular
        $t = $translations->find(null, '1 star');
        $this->assertInstanceOf('Gettext\\Translation', $t);
        $this->assertEquals('%s stars', $t->getPlural());
        $this->assertEquals('1 estrela', $t->getTranslation());
        $this->assertTrue($t->hasPlural());
        $this->assertFalse($t->hasPluralTranslation());
        $t->setPluralTranslation('% estrelas');
        $this->assertTrue($t->hasPluralTranslation());
    }
}
