<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Loader\Loader;
use Gettext\Loader\PoLoader;

class PoLoaderTest extends BasePoLoaderTestCase
{
    protected function createPoLoader(): Loader
    {
        return new PoLoader();
    }

    /**
     * @dataProvider stringDecodeProvider
     * @param mixed $source
     * @param mixed $decoded
     */
    public function testStringDecode($source, $decoded): void
    {
        $this->assertSame($decoded, PoLoader::decode($source));
    }

    public function testCommentsBeforeEmptyComment(): void
    {
        $po = <<<'EOT'
# German translation for myapp.
# Copyright (C) 2024 Example Inc.
#
#, fuzzy
msgid ""
msgstr ""
"Language: de\n"

msgid "Hello"
msgstr "Hallo"
#
# Reviewed by Max
#
#: src/home.php:12
msgid "Bye"
msgstr "Tschüss"
EOT;
        $translations = $this->createPoLoader()->loadString($po);

        $this->assertSame(
            "German translation for myapp.\nCopyright (C) 2024 Example Inc.",
            $translations->getDescription()
        );
        $this->assertTrue($translations->getFlags()->has('fuzzy'));
        $this->assertSame('Hallo', $translations->find(null, 'Hello')->getTranslation());
        $this->assertSame(['Reviewed by Max'], $translations->find(null, 'Bye')->getComments()->toArray());
    }
}
