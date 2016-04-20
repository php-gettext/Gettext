<?php

namespace Gettext\Tests;

use Gettext\Translations;

class TwoTest extends OneTest
{
    protected static $file = 'two';

    const COUNT_TRANSLATIONS = 12;
    const COUNT_EMPTY_TRANSLATIONS = 12;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromPhpCodeFile(static::file('raw.php'));
    }
}
