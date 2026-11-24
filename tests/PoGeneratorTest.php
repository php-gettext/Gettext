<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Generator\PoGenerator;
use Gettext\Translation;
use Gettext\Translations;
use PHPUnit\Framework\TestCase;

class PoGeneratorTest extends TestCase
{
    public function testPoLoader(): void
    {
        $generator = new PoGenerator();
        $translations = Translations::create('my-domain');
        $translations->getFlags()->add('fuzzy');
        $translations->setDescription(
            <<<'EOT'
SOME DESCRIPTIVE TITLE
Copyright (C) YEAR Free Software Foundation, Inc.
This file is distributed under the same license as the PACKAGE package.
FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
EOT
        );
        $translations->setLanguage('gl_ES');
        $translations->getHeaders()
            ->set('Content-Type', 'text/plain; charset=UTF-8')
            ->set('X-Generator', 'PHP-Gettext');

        $translation = Translation::create('context-1', 'Original');
        $translation->getComments()->add('This is a comment');
        $translation->getReferences()->add('/my/template.php', 45);
        $translations->add($translation);

        $translation = Translation::create('context-1', 'Other comment');
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

        // https://github.com/php-gettext/Gettext/issues/244
        $translation = Translation::create(null, "foo\nbar");
        $translation->translate("bar\nbaz");
        $translations->add($translation);

        $result = $generator->generateString($translations);

        $expected = <<<'EOT'
# SOME DESCRIPTIVE TITLE
# Copyright (C) YEAR Free Software Foundation, Inc.
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
#, fuzzy
msgid ""
msgstr ""
"Content-Type: text/plain; charset=UTF-8\n"
"Language: gl_ES\n"
"Plural-Forms: nplurals=2; plural=n != 1;\n"
"X-Domain: my-domain\n"
"X-Generator: PHP-Gettext\n"

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

msgid ""
"foo\n"
"bar"
msgstr ""
"bar\n"
"baz"

EOT;

        $this->assertSame($expected, $result);
    }

    public function stringEncodeProvider(): array
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
    public function testStringEncode(string $encoded, string $decoded): void
    {
        $this->assertSame($encoded, PoGenerator::encode($decoded));
    }

    public function testZeroStrings(): void
    {
        $translations = Translations::create();

        $translation = Translation::create('0', '1');
        $translation->translate('0');
        $translation->setPreviousContext('0');
        $translation->setPreviousOriginal('0');
        $translations->add($translation);

        $translation = Translation::create(null, 'One');
        $translation->setPlural('0');
        $translation->translate('0');
        $translation->translatePlural('0');
        $translations->add($translation);

        $expected = <<<'EOT'
msgid ""
msgstr ""

#| msgctxt "0"
#| msgid "0"
msgctxt "0"
msgid "1"
msgstr "0"

msgid "One"
msgid_plural "0"
msgstr[0] "0"
msgstr[1] "0"

EOT;

        $this->assertSame($expected, (new PoGenerator())->generateString($translations));
    }

    public function testHeaderValuesAreEscaped(): void
    {
        $translations = Translations::create();
        $translations->getHeaders()->set('Last-Translator', '"Josep M. Ferrer" <txemaq@gmail.com>');

        $expected = <<<'EOT'
msgid ""
msgstr ""
"Last-Translator: \"Josep M. Ferrer\" <txemaq@gmail.com>\n"

EOT;

        $this->assertSame($expected, (new PoGenerator())->generateString($translations));
    }
}
