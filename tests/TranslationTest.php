<?php
declare(strict_types = 1);

namespace Gettext\Tests;

use Gettext\Comments;
use Gettext\Flags;
use Gettext\References;
use Gettext\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testTranslation(): void
    {
        $translation = Translation::create('foo', 'bar');

        $this->assertSame('foo', $translation->getContext());
        $this->assertSame('bar', $translation->getOriginal());
        $this->assertSame("foo\004bar", $translation->getId());
        $this->assertFalse($translation->isTranslated());

        $translation->translate('This is the translation');
        $this->assertSame('This is the translation', $translation->getTranslation());
        $this->assertTrue($translation->isTranslated());

        $translation->setPlural('bars');
        $this->assertSame('bars', $translation->getPlural());

        $translation->translatePlural('bars-1', 'bars-2');
        $this->assertSame(['bars-1', 'bars-2'], $translation->getPluralTranslations());

        $this->assertFalse($translation->isDisabled());

        $translation->disable();
        $this->assertTrue($translation->isDisabled());

        $translation->disable(false);
        $this->assertFalse($translation->isDisabled());

        $this->assertInstanceOf(Comments::class, $translation->getComments());
        $this->assertInstanceOf(Comments::class, $translation->getExtractedComments());
        $this->assertInstanceOf(Flags::class, $translation->getFlags());
        $this->assertInstanceOf(References::class, $translation->getReferences());

        $clone = clone $translation;

        $this->assertInstanceOf(Comments::class, $clone->getComments());
        $this->assertInstanceOf(Comments::class, $clone->getExtractedComments());
        $this->assertInstanceOf(Flags::class, $clone->getFlags());
        $this->assertInstanceOf(References::class, $clone->getReferences());

        $this->assertNotSame($translation->getComments(), $clone->getComments());
        $this->assertNotSame($translation->getExtractedComments(), $clone->getExtractedComments());
        $this->assertNotSame($translation->getFlags(), $clone->getFlags());
        $this->assertNotSame($translation->getReferences(), $clone->getReferences());
    }

    public function testMergeTranslation(): void
    {
        $translation1 = Translation::create('context', 'Original');
        $translation1->translate('Orixinal');
        $translation1->getFlags()->add('flag-1', 'flag-2');
        $translation1->getComments()->add('Comment 1', 'Comment 2');
        $translation1->getExtractedComments()->add('Extracted 1');
        $translation1->getReferences()->add('template.php', 34);

        $translation2 = Translation::create('context2', 'Original2');
        $translation2->setPlural('Plural');
        $translation2->translatePlural('Plural 1', 'Plural 2');
        $translation2->getFlags()->add('flag-1', 'flag-3');
        $translation2->getComments()->add('Comment 2', 'Comment 3');
        $translation2->getReferences()
            ->add('template.php', 44)
            ->add('template2.php', 55);

        $merged = $translation1->mergeWith($translation2);

        $this->assertSame('context', $merged->getContext());
        $this->assertSame('Original', $merged->getOriginal());
        $this->assertSame('Plural', $merged->getPlural());
        $this->assertSame(['Plural 1', 'Plural 2'], $merged->getPluralTranslations());

        $this->assertCount(3, $merged->getFlags());
        $this->assertSame(['flag-1', 'flag-2', 'flag-3'], $merged->getFlags()->toArray());

        $this->assertCount(3, $merged->getComments());
        $this->assertSame(['Comment 1', 'Comment 2', 'Comment 3'], $merged->getComments()->toArray());

        $this->assertCount(3, $merged->getReferences());
        $this->assertSame([
            'template.php' => [34, 44],
            'template2.php' => [55],
        ], $merged->getReferences()->toArray());

        $this->assertCount(1, $merged->getExtractedComments());
        $this->assertSame(['Extracted 1'], $merged->getExtractedComments()->toArray());

        $this->assertNotSame($merged, $translation1);
        $this->assertNotSame($merged, $translation2);
    }
}
