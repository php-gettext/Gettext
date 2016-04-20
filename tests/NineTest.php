<?php

namespace Gettext\Tests;

use Gettext\Translations;

class NineTest extends OneTest
{
    protected static $file = 'nine';

    const COUNT_TRANSLATIONS = 9;
    const COUNT_EMPTY_TRANSLATIONS = 9;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromPhpCodeFile(static::file('raw.php'));
    }
}
