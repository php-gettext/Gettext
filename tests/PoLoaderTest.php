<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Loader\PoLoader;
use Gettext\Translation;
use PHPUnit\Framework\TestCase;

class PoLoaderTest extends TestCase
{
    public function testPoLoader()
    {
        $loader = new PoLoader();
        $translations = $loader->loadFile(__DIR__.'/assets/translations.po');

        $description = $translations->getDescription();
        $this->assertSame(<<<'EOT'
SOME DESCRIPTIVE TITLE
Copyright (C) YEAR Free Software Foundation, Inc.
This file is distributed under the same license as the PACKAGE package.
FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
EOT, $description);

        $this->assertSame(['fuzzy'], $translations->getFlags()->toArray());

        $this->assertCount(14, $translations);

        $array = $translations->getTranslations();

        $this->translation1(array_shift($array));
        $this->translation2(array_shift($array));
        $this->translation3(array_shift($array));
        $this->translation4(array_shift($array));
        $this->translation5(array_shift($array));
        $this->translation6(array_shift($array));
        $this->translation7(array_shift($array));
        $this->translation8(array_shift($array));
        $this->translation9(array_shift($array));
        $this->translation10(array_shift($array));
        $this->translation11(array_shift($array));
        $this->translation12(array_shift($array));
        $this->translation13(array_shift($array));
        $this->translation14(array_shift($array));

        $headers = $translations->getHeaders()->toArray();

        $this->assertCount(12, $headers);

        $this->assertSame('text/plain; charset=UTF-8', $headers['Content-Type']);
        $this->assertSame('8bit', $headers['Content-Transfer-Encoding']);
        $this->assertSame('', $headers['POT-Creation-Date']);
        $this->assertSame('', $headers['PO-Revision-Date']);
        $this->assertSame('', $headers['Last-Translator']);
        $this->assertSame('', $headers['Language-Team']);
        $this->assertSame('1.0', $headers['MIME-Version']);
        $this->assertSame('bs', $headers['Language']);
        $this->assertSame(
            'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2);',
            $headers['Plural-Forms']
        );
        $this->assertSame('Poedit 1.6.5', $headers['X-Generator']);
        $this->assertSame('gettext generator test', $headers['Project-Id-Version']);
        $this->assertSame('testingdomain', $headers['X-Domain']);

        $this->assertSame('testingdomain', $translations->getDomain());
        $this->assertSame('bs', $translations->getLanguage());
    }

    private function translation1(Translation $translation)
    {
        $this->assertSame(
            'Ensure this value has at least %(limit_value)d character (it has %sd).',
            $translation->getOriginal()
        );
        $this->assertSame(
            'Ensure this value has at least %(limit_value)d characters (it has %sd).',
            $translation->getPlural()
        );
        $this->assertSame('', $translation->getTranslation());
        $this->assertSame(['', ''], $translation->getPluralTranslations());
    }

    private function translation2(Translation $translation)
    {
        $this->assertSame(
            'Ensure this value has at most %(limit_value)d character (it has %sd).',
            $translation->getOriginal()
        );
        $this->assertSame(
            'Ensure this value has at most %(limit_value)d characters (it has %sd).',
            $translation->getPlural()
        );
        $this->assertSame('', $translation->getTranslation());
        $this->assertSame(['', ''], $translation->getPluralTranslations());
    }

    private function translation3(Translation $translation)
    {
        $this->assertSame('%ss must be unique for %ss %ss.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('%ss mora da bude jedinstven za %ss %ss.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation4(Translation $translation)
    {
        $this->assertSame('and', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('i', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertSame(['c-format'], $translation->getFlags()->toArray());
    }

    private function translation5(Translation $translation)
    {
        $this->assertSame('Value %sr is not a valid choice.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertSame(['This is a extracted comment'], $translation->getExtractedComments()->toArray());
    }

    private function translation6(Translation $translation)
    {
        $this->assertSame('This field cannot be null.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Ovo polje ne može ostati prazno.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(1, $translation->getReferences());
        $this->assertSame(['C:/Users/Me/Documents/foo2.php' => [1]], $translation->getReferences()->toArray());
    }

    private function translation7(Translation $translation)
    {
        $this->assertSame('This field cannot be blank.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Ovo polje ne može biti prazno.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(1, $translation->getReferences());
        $this->assertSame(['C:/Users/Me/Documents/foo1.php' => []], $translation->getReferences()->toArray());
    }

    private function translation8(Translation $translation)
    {
        $this->assertSame('Field of type: %ss', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Polje tipa: %ss', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(2, $translation->getReferences());
        $this->assertSame([
                'attributes/address/composer.php' => [8],
                'attributes/address/form.php' => [7],
            ],
            $translation->getReferences()->toArray()
        );
    }

    private function translation9(Translation $translation)
    {
        $this->assertSame('Integer', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Cijeo broj', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(0, $translation->getReferences());
        $this->assertCount(1, $translation->getComments());
        $this->assertSame(['a simple line comment is above'], $translation->getComments()->toArray());
    }

    private function translation10(Translation $translation)
    {
        $this->assertSame('{test1}', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("test1\n<div>\n test2\n</div>\ntest3", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(0, $translation->getComments());
        $this->assertCount(3, $translation->getReferences());
        $this->assertSame([
                '/var/www/test/test.php' => [96, 97],
                '/var/www/test/test2.php' => [98],
            ],
            $translation->getReferences()->toArray()
        );
    }

    private function translation11(Translation $translation)
    {
        $this->assertSame('{test2}', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("test1\n<div>\n test2\n</div>\ntest3", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(0, $translation->getComments());
        $this->assertCount(1, $translation->getReferences());
        $this->assertSame(
            ['/var/www/test/test.php' => [96]],
            $translation->getReferences()->toArray()
        );
    }

    private function translation12(Translation $translation)
    {
        $this->assertSame('Multibyte test', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('日本人は日本で話される言語です！', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(0, $translation->getComments());
        $this->assertCount(0, $translation->getReferences());
    }

    private function translation13(Translation $translation)
    {
        $this->assertSame('Tabulation test', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("FIELD\tFIELD", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
        $this->assertCount(0, $translation->getComments());
        $this->assertCount(0, $translation->getReferences());
    }

    private function translation14(Translation $translation)
    {
        $this->assertSame('%s has been added to your cart.', $translation->getOriginal());
        $this->assertSame('%s have been added to your cart.', $translation->getPlural());
        $this->assertSame('%s has been added to your cart.', $translation->getTranslation());
        $this->assertSame(['%s have been added to your cart.'], $translation->getPluralTranslations());
        $this->assertCount(1, $translation->getComments());
        $this->assertCount(0, $translation->getReferences());
    }

    public function stringDecodeProvider()
    {
        return [
            ['"test"', 'test'],
            ['"\'test\'"', "'test'"],
            ['"Special chars: \\n \\t \\\\ "', "Special chars: \n \t \\ "],
            ['"Newline\nSlash and n\\\\nend"', "Newline\nSlash and n\\nend"],
            ['"Quoted \\"string\\" with %s"', 'Quoted "string" with %s'],
            ['"\\\\x07 - aka \\\\a: \\a"', "\\x07 - aka \\a: \x07"],
            ['"\\\\x08 - aka \\\\b: \\b"', "\\x08 - aka \\b: \x08"],
            ['"\\\\x09 - aka \\\\t: \\t"', "\\x09 - aka \\t: \t"],
            ['"\\\\x0a - aka \\\\n: \\n"', "\\x0a - aka \\n: \n"],
            ['"\\\\x0b - aka \\\\v: \\v"', "\\x0b - aka \\v: \x0b"],
            ['"\\\\x0c - aka \\\\f: \\f"', "\\x0c - aka \\f: \x0c"],
            ['"\\\\x0d - aka \\\\r: \\r"', "\\x0d - aka \\r: \r"],
            ['"\\\\x22 - aka \\": \\""', '\x22 - aka ": "'],
            ['"\\\\x5c - aka \\\\: \\\\"', '\\x5c - aka \\: \\'],
        ];
    }

    /**
     * @dataProvider stringDecodeProvider
     * @param mixed $source
     * @param mixed $decoded
     */
    public function testStringDecode($source, $decoded)
    {
        $this->assertSame($decoded, PoLoader::decode($source));
    }
}
