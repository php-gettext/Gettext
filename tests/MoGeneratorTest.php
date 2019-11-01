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
    public function testMoGenerator()
    {
        $generator = (new MoGenerator())->includeHeaders();
        $loader = new MoLoader();

        $translations = Translations::create('my-domain');
        $translations->setLanguage('gl_ES');
        $translations->getHeaders()
            ->set('Content-Type', 'text/plain; charset=UTF-8')
            ->set('X-Generator', 'PHP-Gettext');

        $translation = Translation::create('context-1', 'Original');
        $translation->translate('Orixintal');
        $translations->add($translation);

        $translation = Translation::create('context-1', 'Other comment', 'Other comments');
        $translation->translate('Outro comentario');
        $translation->translatePlural('Outros comentarios');
        $translations->add($translation);

        $translation = Translation::create(null, 'Disabled comment');
        $translation->disable();
        $translation->translate('Comentario deshabilitado');
        $translations->add($translation);

        $mo = $generator->generateString($translations);
        $expected = file_get_contents(__DIR__.'/assets/mo-generator-result.mo');

        $this->assertSame($expected, $mo);

        $result = $loader->loadString($mo);

        $this->assertCount(2, $result);
        $this->assertCount(5, $result->getHeaders());
    }
}
