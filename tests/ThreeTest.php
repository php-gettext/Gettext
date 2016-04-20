<?php

namespace Gettext\Tests;

use Gettext\Translations;

class ThreeTest extends OneTest
{
    protected static $file = 'three';

    const COUNT_TRANSLATIONS = 13;
    const COUNT_EMPTY_TRANSLATIONS = 3;
    const COUNT_HEADERS = 13;

    protected function getParsed()
    {
        return Translations::fromPoFile(static::file('raw.po'));
    }
}
