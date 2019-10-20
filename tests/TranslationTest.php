<?php

namespace Gettext\Tests;

use Gettext\Translation;
use Gettext\Comments;
use Gettext\Flags;
use Gettext\References;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function testTranslation()
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
}
