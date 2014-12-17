<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class PoExtractorTest extends PHPUnit_Framework_TestCase
{
	public function testPluralHeader()
	{
		//Extract translations
	    $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/plurals.po');

	    $this->assertInstanceOf('Gettext\\Translations', $translations);

	    $pluralHeader = "nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);";
        $this->assertEquals($pluralHeader, $translations->getHeader('Plural-Forms'), "Plural form did not get extracted correctly");
	}


	public function testMultilineHeader()
	{
		//Extract translations
	    $translations = Gettext\Extractors\Po::fromFile(__DIR__.'/files/po.po');

	    $this->assertInstanceOf('Gettext\\Translations', $translations);

	    $pluralHeader = "nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);";
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

        $this->assertNull($translations->getLanguage(), 'Something erroneously set for language');
        $this->assertNull($translations->getDomain(), 'Something erroneously set for domain');
	}
}
