<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Loader\MoLoader;
use Gettext\Translation;
use PHPUnit\Framework\TestCase;

class MoLoaderTest extends TestCase
{
    public function testMoLoader()
    {
        $loader = new MoLoader();
        $translations = $loader->loadFile(__DIR__.'/assets/translations.mo');

        $this->assertCount(11, $translations);

        $array = $translations->getTranslations();

        $this->translation0(array_shift($array));
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

    private function translation0(Translation $translation)
    {
        $this->assertSame('%s has been added to your cart.', $translation->getOriginal());
        $this->assertSame('%s have been added to your cart.', $translation->getPlural());
        $this->assertSame('%s has been added to your cart.', $translation->getTranslation());
        $this->assertCount(1, $translation->getPluralTranslations());
    }

    private function translation1(Translation $translation)
    {
        $this->assertSame('%ss must be unique for %ss %ss.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('%ss mora da bude jedinstven za %ss %ss.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation2(Translation $translation)
    {
        $this->assertSame('Field of type: %ss', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Polje tipa: %ss', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation3(Translation $translation)
    {
        $this->assertSame('Integer', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Cijeo broj', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation4(Translation $translation)
    {
        $this->assertSame('Multibyte test', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('日本人は日本で話される言語です！', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation5(Translation $translation)
    {
        $this->assertSame('Tabulation test', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("FIELD\tFIELD", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation6(Translation $translation)
    {
        $this->assertSame('This field cannot be blank.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Ovo polje ne može biti prazno.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation7(Translation $translation)
    {
        $this->assertSame('This field cannot be null.', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('Ovo polje ne može ostati prazno.', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation8(Translation $translation)
    {
        $this->assertSame('and', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame('i', $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation9(Translation $translation)
    {
        $this->assertSame('{test1}', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("test1\n<div>\n test2\n</div>\ntest3", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }

    private function translation10(Translation $translation)
    {
        $this->assertSame('{test2}', $translation->getOriginal());
        $this->assertNull($translation->getPlural());
        $this->assertSame("test1\n<div>\n test2\n</div>\ntest3", $translation->getTranslation());
        $this->assertCount(0, $translation->getPluralTranslations());
    }
}
