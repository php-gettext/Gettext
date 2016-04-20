<?php

namespace Gettext\Tests;

use Gettext\Translations;

class SevenTest extends OneTest
{
    protected static $file = 'seven';

    const COUNT_TRANSLATIONS = 8;
    const COUNT_EMPTY_TRANSLATIONS = 0;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromPoFile(static::file('raw.po'));
    }
}
