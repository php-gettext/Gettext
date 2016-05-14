<?php

namespace Gettext\Tests;

use Gettext\Translations;

class Asset7Test extends Asset1Test
{
    protected static $directory = '7';
    protected static $input = 'Po';

    const COUNT_TRANSLATIONS = 8;
    const COUNT_EMPTY_TRANSLATIONS = 0;
    const COUNT_HEADERS = 8;
}
