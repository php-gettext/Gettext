<?php

namespace Gettext\Tests;

use Gettext\Translations;

class T2 extends T1
{
    protected static $directory = '2';
    protected static $input = 'PhpCode';

    const COUNT_TRANSLATIONS = 12;
    const COUNT_EMPTY_TRANSLATIONS = 12;
    const COUNT_HEADERS = 8;
}
