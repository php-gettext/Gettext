<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Generator\MoGenerator;
use Gettext\Loader\MoLoader;
use Gettext\Translation;
use Gettext\Translations;
use PHPUnit\Framework\TestCase;

class MoGeneratorTest extends TestCase
{
    public function testMoGenerator(): void
    {
        $generator = (new MoGenerator())->includeHeaders();
        $loader = new MoLoader();

        $translations = Translations::create('my-domain');
        $translations->setLanguage('gl_ES');
        $translations->getHeaders()
            ->set('Content-Type', 'text/plain; charset=UTF-8')
            ->set('X-Generator', 'PHP-Gettext');

        $translation = Translation::create('context-1', 'Original');
        $translation->translate('Orixinal');
        $translations->add($translation);

        $translation = Translation::create('context-1', 'Other comment');
        $translation->translate('Outro comentario');
        $translation->translatePlural('Outros comentarios');
        $translations->add($translation);

        $translation = Translation::create(null, 'Disabled comment');
        $translation->disable();
        $translation->translate('Comentario deshabilitado');
        $translations->add($translation);

        $translation = Translation::create(null, '15');
        $translation->translate('15');
        $translations->add($translation);

        $translation = Translation::create(null, '123456');
        $translation->translate('12345');
        $translations->add($translation);

        $mo = $generator->generateString($translations);
        $expected = file_get_contents(__DIR__.'/assets/mo-generator-result.mo');

        $this->assertSame($expected, $mo);

        $result = $loader->loadString($mo);

        $this->assertCount(4, $result);
        $this->assertCount(5, $result->getHeaders());
    }

    public function testFuzzyTranslationsAreSkippedByDefault(): void
    {
        $translations = Translations::create();
        $translation = Translation::create(null, 'New text');
        $translation->translate('Texte ancien');
        $translation->getFlags()->add('fuzzy');
        $translations->add($translation);

        $loader = new MoLoader();

        $this->assertCount(0, $loader->loadString((new MoGenerator())->generateString($translations)));
        $this->assertCount(1, $loader->loadString((new MoGenerator())->includeFuzzy()->generateString($translations)));
    }

    public function testPluralFormsAreWrittenByDefault(): void
    {
        $translations = Translations::create();
        $translations->setLanguage('ru');
        $translation = Translation::create(null, '%d file');
        $translation->setPlural('%d files');
        $translation->translate('%d файл');
        $translation->translatePlural('%d файла', '%d файлов');
        $translations->add($translation);

        $mo = (new MoGenerator())->generateString($translations);
        $result = (new MoLoader())->loadString($mo);

        $this->assertSame($translations->getHeaders()->getPluralForm(), $result->getHeaders()->getPluralForm());
        $this->assertSame(['%d файла', '%d файлов'], $result->find(null, '%d file')->getPluralTranslations());
    }

    public function testZeroStrings(): void
    {
        $translations = Translations::create();

        $translation = Translation::create('0', '1');
        $translation->translate('0');
        $translations->add($translation);

        $translation = Translation::create(null, 'One');
        $translation->setPlural('0');
        $translation->translate('Singular');
        $translation->translatePlural('Plural');
        $translations->add($translation);

        $result = (new MoLoader())->loadString((new MoGenerator())->generateString($translations));

        $this->assertCount(2, $result);

        $translation = $result->find('0', '1');
        $this->assertNotNull($translation);
        $this->assertSame('0', $translation->getTranslation());

        $translation = $result->find(null, 'One');
        $this->assertNotNull($translation);
        $this->assertSame('0', $translation->getPlural());
        $this->assertSame(['Plural'], $translation->getPluralTranslations());
    }
}
