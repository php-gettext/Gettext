<?php

namespace Gettext\Tests;

use Gettext\Translations;

class T6 extends T1
{
    protected static $directory = '6';
    protected static $input = 'Twig';

    const COUNT_TRANSLATIONS = 10;
    const COUNT_EMPTY_TRANSLATIONS = 10;
    const COUNT_HEADERS = 8;
}
