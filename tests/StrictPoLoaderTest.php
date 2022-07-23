<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Loader\StrictPoLoader;

class StrictPoLoaderTest extends BasePoLoaderTestCase
{
    protected function createPoLoader(): StrictPoLoader
    {
        return new StrictPoLoader();
    }

    public function stringDecodeProvider(): array
    {
        return array_merge(parent::stringDecodeProvider(), [
            // Octal
            ['"Up to 3 digits, 1 will be skipped \\0\\00\\0001"', "Up to 3 digits, 1 will be skipped \0\0\0001"],
            ['"\\101\\102"', 'AB'],
            // Hexadecimal
            ['"Works with a single character \\x41\\xA\\xD\\x5A\\x5a"', "Works with a single character A\n\rZZ"],
            ['"Last hex pair: tab = \\x12345678AAAAaaaa09"', "Last hex pair: tab = \t"],
            // Unicode
            ['"UTF-8, up to 4 digits \u00c0A\u00C1 \u0C0\u41\uC1"', 'UTF-8, up to 4 digits ÀAÁ ÀAÁ'],
            ['"UTF-32, up to 8 digits \U000000c0A\U00C1 \U0C0\U41\UC1"', 'UTF-32, up to 8 digits ÀAÁ ÀAÁ'],
        ]);
    }

    public function testCollapsedSyntax(): void
    {
        $po = "#   comment\nmsgctxt\"ctx\"msgid\"original\"msgstr\"trans\"\"lation\"";
        $translations = $this->createPoLoader()->loadString($po);
        $this->assertEquals($translations->find('ctx', 'original')->getTranslation(), 'translation');
        $this->assertEquals($translations->find('ctx', 'original')->getComments()->toArray()[0], '  comment');
    }

    public function testPreviousTranslation(): void
    {
        $po = '#| msgctxt "previous ctx"
        #| msgid "previous original"
        #| msgid_plural "previous plural"
        msgctxt "ctx"
        msgid "original"
        msgid_plural "plural"
        msgstr "translation"';
        $translations = $this->createPoLoader()->loadString($po);

        $translation = $translations->find('ctx', 'original');
        $this->assertNotNull($translation);
        $this->assertEquals($translation->getContext(), 'ctx');
        $this->assertEquals($translation->getOriginal(), 'original');
        $this->assertEquals($translation->getPlural(), 'plural');
        $this->assertEquals($translation->getTranslation(), 'translation');

        $this->assertEquals($translation->getPreviousContext(), 'previous ctx');
        $this->assertEquals($translation->getPreviousOriginal(), 'previous original');
        $this->assertEquals($translation->getPreviousPlural(), 'previous plural');

    }

    public function badFormattedPoProvider(): array
    {
        return [
            'Duplicated entry' => [
                '/Duplicated entry/',
                'msgid"original"
                msgstr"translation"
                
                msgid"original"
                msgstr"translation 2"',
            ],
            'msgstr before msgid' => [
                '/Expected msgid/',
                'msgstr "translation"
                msgid "original"',
            ],
            'Comments should come before the definitions' => [
                '/Expected msgstr/',
                'msgid "original"
                # Unexpected comment
                msgstr "translation"',
            ],
            'msgid_plural requires an indexed msgstr' => [
                '/Expected character "\\["/',
                'msgid "original"
                msgid_plural "plural"
                msgstr "translation"',
            ],
            'msgstr with a bad index' => [
                '/The msgstr has an invalid index/',
                'msgid "original"
                msgid_plural "plural"
                msgstr[0] "translation"
                msgstr[2] "translation"',
            ],
            'msgstr with a bad index 2' => [
                '/Expected character "]"/',
                'msgid "original"
                msgid_plural "plural"
                msgstr[0] "translation"
                msgstr[1s] "translation"',
            ],
            'Incomplete translation' => [
                '/Expected msgstr/',
                'msgid "original"',
            ],
            'Bad quoted msgid' => [
                '/Expected an opening quote/',
                'msgid original
                msgstr "translation"',
            ],
            'Unquoted newline' => [
                '/Newline character must be escaped/',
                'msgid "original"
                msgstr "trans
                lation"',
            ],
            'Bad escaped octal' => [
                '/Invalid quoted character/',
                'msgid "original"
                msgstr "translation\8"',
            ],
            'Out of range octal' => [
                '/Octal value out of range/',
                'msgid "original"
                msgstr "translation\777"',
            ],
            'Bad escaped hex' => [
                '/Expected at least one occurrence of hexadecimal/',
                'msgid "original"
                msgstr "translation\xGG"',
            ],
            'Bad escaped hex' => [
                '/Expected at least one occurrence of hexadecimal/',
                'msgid "original"
                msgstr "translation\xGG"',
            ],
            'Bad escaped unicode' => [
                '/Expected at least one occurrence of hexadecimal/',
                'msgid "original"
                msgstr "translation\uZZ"',
            ],
            'Disabled translations (#~) cannot appear after previous translations (#|)' => [
                '/Inconsistent use of #~/',
                '#|msgid "previous"
                #~msgid "disabled"
                #~msgstr "disabled translation"
                msgid "original"
                msgstr "translation"',
            ],
            'Invalid identifier' => [
                '/Expected msgid/',
                'unknown "original"
                msgstr "translation"',
            ],
            'msgctxt of a previous translation must come before its msgid' => [
                '/Cannot redeclare the previous comment/',
                '#|msgid "previous"
                #|msgctxt "previous context"
                #|msgid_plural "previous context"
                msgid "original"
                msgstr "translation"',
            ],
            // The checks below depends on the $throwOnWarning = true
            'msgid, msgid_plural and msgstr cannot begin nor end with newline' => [
                '/msgstr cannot start nor end with a newline/',
                'msgid "original"
                msgstr "translation\n"',
                true,
            ],
            'Missing header' => [
                '/The loaded string has no header translation/',
                'msgid "original"
                msgstr "translation"',
                true,
            ],
            'Duplicated header' => [
                '/Header already defined/',
                'msgid ""
                msgstr "Header: \\n"
                "Header: \\n"',
                true,
            ],
            'Malformed header name' => [
                '/Malformed header name/',
                'msgid ""
                msgstr "Header\\n"',
                true,
            ],
            'Missing standard headers Language/Plural-Forms/Content-Type' => [
                '/header not declared or empty/',
                'msgid ""
                msgstr "Header: Value\\n"',
                true,
            ],
            'Two plural forms with just one plural translation' => [
                '/The translation doesn\'t have all the \\d+ plural forms/',
                'msgid ""
                msgstr "Language: en_US\n"
                "Content-Type: text/plain; charset=UTF-8\n"
                "Plural-Forms: nplurals=2; plural=n != 1;\n"

                msgid "original"
                msgid_plural "plural"
                msgstr[0] "translation"',
                true,
            ],
        ];
    }

    /**
     * @dataProvider badFormattedPoProvider
     */
    public function testBadFormattedPo(string $exceptionPattern, string $po, bool $throwOnWarning = false): void
    {
        $this->expectExceptionMessageMatches($exceptionPattern);
        $this->createPoLoader()->loadString($po, null, $throwOnWarning);
    }
}
