<?php

namespace Gettext\Tests;

use Gettext\Translations;
use Gettext\Loader\PoLoader;
use PHPUnit\Framework\TestCase;

class PoLoaderTest extends TestCase
{
    public function testPoLoader()
    {
        $loader = new PoLoader();
        $loader->loadFile(__DIR__.'/assets/translations.po');
        $translations = $loader->getTranslations();
        
        $this->assertCount(13, $translations);
    }
}
