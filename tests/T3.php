<?php

namespace Gettext\Tests;

use Gettext\Translations;

class T3 extends T1
{
    protected static $directory = '3';
    protected static $input = 'Po';

    const COUNT_TRANSLATIONS = 13;
    const COUNT_EMPTY_TRANSLATIONS = 3;
    const COUNT_HEADERS = 13;
}
