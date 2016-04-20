<?php

namespace Gettext\Tests;

use Gettext\Translations;

class FiveTest extends OneTest
{
    protected static $file = 'five';

    const COUNT_TRANSLATIONS = 13;
    const COUNT_EMPTY_TRANSLATIONS = 3;
    const COUNT_HEADERS = 10;

    protected function getParsed()
    {
        return Translations::fromJedFile(static::file('raw.jed'));
    }
}
