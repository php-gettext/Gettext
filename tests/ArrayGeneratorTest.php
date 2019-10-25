<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Translations;
use Gettext\Generator\ArrayGenerator;
use PHPUnit\Framework\TestCase;

class ArrayGeneratorTest extends TestCase
{
    public function testArrayGenerator()
    {
        $translations = Translations::create('testingdomain');
        $translations->setLanguage('ru');
        $translations->getHeaders()
            ->set('Content-Transfer-Encoding', '8bit')
            ->set('Content-Type', 'text/plain; charset=UTF-8')
            ->set('Language-Team', '')
            ->set('Last-Translator', '')
            ->set('MIME-Version', '1.0')
            ->set('PO-Revision-Date', '')
            ->set('POT-Creation-Date', '')
            ->set('Project-Id-Version', 'gettext generator test');

        $translation = Translation::create(null, 'Ensure this value has at least %(limit_value)d character (it has %sd).');
        $translations->add($translation);

        $translation = Translation::create(null, '%ss must be unique for %ss %ss.');
        $translation->translate('%ss mora da bude jedinstven za %ss %ss.');
        $translations->add($translation);

        $translation = Translation::create('other-context', '日本人は日本で話される言語です！');
        $translation->translate('singular');
        $translation->translatePlural('plural1', 'plural2', 'plural3');
        $translations->add($translation);


        $generator = new ArrayGenerator();
        $array = $generator->generateArray($translations);

        $expected = [
            'domain' => 'testingdomain',
            'plural-forms' => 'nplurals=3; plural=(n % 10 == 1 && n % 100 != 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : 2);',
            'messages' => [
                '' => [
                    '' => 'Content-Transfer-Encoding: 8bit
Content-Type: text/plain; charset=UTF-8
Language: ru
Language-Team: 
Last-Translator: 
MIME-Version: 1.0
PO-Revision-Date: 
POT-Creation-Date: 
Plural-Forms: nplurals=3; plural=(n % 10 == 1 && n % 100 != 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : 2);
Project-Id-Version: gettext generator test
X-Domain: testingdomain',
                    'Ensure this value has at least %(limit_value)d character (it has %sd).' => null,
                    '%ss must be unique for %ss %ss.' => '%ss mora da bude jedinstven za %ss %ss.',
                ],
                'other-context' => [
                    '日本人は日本で話される言語です！' => ['singular', 'plural1', 'plural2'],
                ],
            ],
        ];
        
        $this->assertSame($expected, $array);
    }
}
