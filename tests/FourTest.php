<?php

namespace Gettext\Tests;

use Gettext\Translations;

class FourTest extends OneTest
{
    protected static $file = 'four';

    const COUNT_TRANSLATIONS = 11;
    const COUNT_EMPTY_TRANSLATIONS = 11;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromBladeFile(static::file('blade.php'));
    }
}
