<?php

namespace Gettext\Tests;

use Gettext\Translations;

class EightTest extends OneTest
{
    protected static $file = 'eight';

    const COUNT_TRANSLATIONS = 7;
    const COUNT_EMPTY_TRANSLATIONS = 7;
    const COUNT_HEADERS = 8;

    protected function getParsed()
    {
        return Translations::fromJsCodeFile(static::file('js'));
    }
}
