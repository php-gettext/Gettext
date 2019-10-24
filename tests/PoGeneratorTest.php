<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Translations;
use Gettext\Generator\PoGenerator;
use PHPUnit\Framework\TestCase;

class PoGeneratorTest extends TestCase
{
    public function testPoLoader()
    {
        $generator = new PoGenerator();
        $translations = new Translations('my-domain');
        $translations->setLanguage('gl_ES');
        $translations->getHeaders()
            ->set('Content-Type', 'text/plain; charset=UTF-8')
            ->set('X-Generator', 'PHP-Gettext');
        
        
        $translation = Translation::create('context-1', 'Original');
        $translation->getComments()->add('This is a comment');
        $translation->getReferences()->add('/my/template.php', 45);
        $translations->add($translation);
        
        $translation = Translation::create('context-1', 'Other comment', 'Other comments');
        $translation->translate('Outro comentario');
        $translation->translatePlural('Outros comentarios');
        $translation->getExtractedComments()->add('Not sure about this');
        $translation->getFlags()->add('c-code');
        $translations->add($translation);

        $translation = Translation::create(null, 'Disabled comment');
        $translation->disable();
        $translation->translate('Comentario deshabilitado');
        $translation->getComments()->add('This is a disabled comment');
        $translations->add($translation);
        
        $result = $generator->generateString($translations);

        $expected = <<<EOT
        msgid ""
        msgstr ""
        "Content-Type: text/plain; charset=UTF-8\\n"
        "Language: gl_ES\\n"
        "Plural-Forms: nplurals=2; plural=n != 1;\\n"
        "X-Domain: my-domain\\n"
        "X-Generator: PHP-Gettext\\n"

        # This is a comment
        #: /my/template.php:45
        msgctxt "context-1"
        msgid "Original"
        msgstr ""

        #. Not sure about this
        #, c-code
        msgctxt "context-1"
        msgid "Other comment"
        msgstr "Outro comentario"

        # This is a disabled comment
        #~ msgid "Disabled comment"
        #~ msgstr "Comentario deshabilitado"

        EOT;

        $this->assertSame($expected, $result);
    }

    public function stringEncodeProvider()
    {
        return [
            ['"test"', 'test'],
            ['"\'test\'"', "'test'"],
            ['"Special chars: \\n \\t \\\\ "', "Special chars: \n \t \\ "],
            ['"Newline\nSlash and n\\\\nend"', "Newline\nSlash and n\\nend"],
            ['"Quoted \\"string\\" with %s"', 'Quoted "string" with %s'],
        ];
    }

    /**
     * @dataProvider stringEncodeProvider
     */
    public function testStringEncode($encoded, $decoded)
    {
        $this->assertSame($encoded, PoGenerator::encode($decoded));
    }
}
