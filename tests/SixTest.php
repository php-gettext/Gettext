<?php

namespace Gettext\Tests;

use Gettext\Translations;

class SixTest extends OneTest
{
    protected static $file = 'six';

    const COUNT_TRANSLATIONS = 10;
    const COUNT_EMPTY_TRANSLATIONS = 10;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromTwigFile(static::file('twig.php'));
    }
}
