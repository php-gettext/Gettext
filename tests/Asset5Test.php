<?php

namespace Gettext\Tests;

use Gettext\Translations;

class Asset5Test extends Asset1Test
{
    protected static $directory = '5';
    protected static $input = 'Jed';

    const COUNT_TRANSLATIONS = 13;
    const COUNT_EMPTY_TRANSLATIONS = 3;
    const COUNT_HEADERS = 10;
}
