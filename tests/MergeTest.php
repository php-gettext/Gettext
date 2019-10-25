<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Translations;
use Gettext\Merge;
use PHPUnit\Framework\TestCase;
use Brick\VarExporter\VarExporter;

class MergeTest extends TestCase
{
    private static function createPOT(): Translations
    {
        $translations = Translations::create('my-domain');
        $translations->getHeaders()
            ->set('POT-Creation-Date', '2019-10-10 10:10:10')
            ->set('Last-Translator', '')
            ->set('X-Foo', 'foo')
            ->set('X-Generator', 'PHP Gettext scanner');

        $translation = Translation::create(null, 'title');
        $translation->getReferences()->add('template.php', 3);
        $translations->add($translation);

        $translation = Translation::create(null, 'intro');
        $translation->getReferences()->add('template.php', 4);
        $translations->add($translation);

        $translation = Translation::create(null, 'one comment');
        $translation->setPlural('%s comments');
        $translation->getReferences()->add('template.php', 5);
        $translation->getExtractedComments()->add('Number of comments of the article');
        $translations->add($translation);

        $translation = Translation::create(null, 'This is a flagged element');
        $translation->getReferences()->add('template.php', 10);
        $translation->getFlags()->add('c-code');
        $translations->add($translation);

        $translation = Translation::create(null, 'This is a new translation');
        $translation->getReferences()->add('template.php', 11);
        $translations->add($translation);

        return $translations;
    }

    private static function createPO(): Translations
    {
        $translations = Translations::create('my-domain');
        $translations->getHeaders()
            ->set('Last-Translator', 'Oscar')
            ->set('X-Generator', 'PHP Gettext scanner')
            ->set('Language-Team', 'My Team')
            ->set('X-Foo', 'bar')
            ->set('Language', 'gl_ES');

        $translation = Translation::create(null, 'title');
        $translation->getReferences()->add('template.php', 2);
        $translation->translate('Título');
        $translations->add($translation);

        $translation = Translation::create(null, 'subtitle');
        $translation->getReferences()->add('template.php', 2);
        $translation->translate('Subtítulo');
        $translations->add($translation);

        $translation = Translation::create(null, 'intro');
        $translation->getReferences()->add('template.php', 4);
        $translation->getComments()->add('Disabled comment');
        $translation->translate('Intro');
        $translation->disable();
        $translations->add($translation);

        $translation = Translation::create(null, 'one comment');
        $translation->setPlural('%s comments');
        $translation->getReferences()->add('template.php', 6);
        $translation->getExtractedComments()->add('Number of comments of the article');
        $translation->translate('Un comentario');
        $translation->translatePlural('%s comentarios');
        $translations->add($translation);

        $translation = Translation::create(null, 'This is a flagged element');
        $translation->getFlags()->add('a-code');
        $translation->getComments()->add('This is a comment');
        $translations->add($translation);

        return $translations;
    }

    public function testNoStrategy()
    {
        $pot = self::createPOT();
        $po = self::createPO();

        $merged = $pot->mergeWith($po);

        $this->assertSnapshot(__FUNCTION__, $merged);
    }

    /**
     * We want to use the scanner to fetch new entries and complete them with PO files
     */
    public function testScanAndLoadStrategy()
    {
        $pot = self::createPOT();
        $po = self::createPO();

        $strategy = Merge::HEADERS_OVERRIDE         // Override the headers with the PO values
                  | Merge::TRANSLATIONS_OURS        // Keep only the scanned entries
                  | Merge::TRANSLATIONS_OVERRIDE    // Apply the changes of the PO
                  | Merge::EXTRACTED_COMMENTS_OURS  // Keep only the extracted comments
                  | Merge::REFERENCES_OURS          // Keep only the scanned references
                  | Merge::FLAGS_THEIRS             // Keep the flags in PO
                  | Merge::COMMENTS_THEIRS;         // Keep the comments in PO

        $this->assertSame($strategy, Merge::SCAN_AND_LOAD);

        $merged = $pot->mergeWith($po, $strategy);

        $this->assertSnapshot(__FUNCTION__, $merged);
    }

    private function assertSnapshot(string $name, Translations $translations, bool $forceCreate = false)
    {
        $file = __DIR__."/snapshots/{$name}.php";
        $array = $translations->toArray();

        if (!is_file($file) || $forceCreate) {
            $code = sprintf('<?php %s', VarExporter::export($array, true));
            file_put_contents($file, $code);
        }

        $expected = require $file;
        $this->assertSame($expected, $array);
    }
}
