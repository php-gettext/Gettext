<?php

namespace Gettext\Tests;

use Gettext\Translations;

class T4 extends T1
{
    protected static $directory = '4';
    protected static $input = 'Blade';

    const COUNT_TRANSLATIONS = 11;
    const COUNT_EMPTY_TRANSLATIONS = 11;
    const COUNT_HEADERS = 8;
}
