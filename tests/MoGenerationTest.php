<?php
include_once dirname(__DIR__).'/src/autoloader.php';

class MoGenerationTest extends PHPUnit_Framework_TestCase
{
    public function testMoGeneration()
    {
        $originalTranslations = Gettext\Translations::fromPoFile(__DIR__.'/files/po.po');
        $this->assertInstanceOf('Gettext\\Translations', $originalTranslations);

        $moData = $originalTranslations->toMoString();
        $this->assertInternalType('string', $moData);
        $this->assertGreaterThan(0, strlen($moData));

        $decompiledTranslations = Gettext\Translations::fromMoString($moData);
        $this->assertInstanceOf('Gettext\\Translations', $decompiledTranslations);

        $this->assertEquals($originalTranslations->getHeaders(), $decompiledTranslations->getHeaders());
        $this->assertSame($originalTranslations->count(), $decompiledTranslations->count());

        foreach ($originalTranslations as $originalTranslation) {
            $decompiledTranslation = $decompiledTranslations->find($originalTranslation->getContext(), $originalTranslation->getOriginal());
            $this->assertInstanceOf('Gettext\\Translation', $decompiledTranslation, 'Translation not found: context="'.$originalTranslation->getContext().'", original="'.$originalTranslation->getOriginal().'"');
            $this->assertSame($originalTranslation->getTranslation(), $decompiledTranslation->getTranslation());
            $this->assertSame($originalTranslation->getPluralTranslation(), $decompiledTranslation->getPluralTranslation());
        }
    }
}
