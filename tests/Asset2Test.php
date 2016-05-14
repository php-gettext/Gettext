<?php

namespace Gettext\Tests;

class Asset2Test extends Asset1Test
{
    protected static $directory = '2';
    protected static $input = 'PhpCode';

    const COUNT_TRANSLATIONS = 12;
    const COUNT_EMPTY_TRANSLATIONS = 12;
    const COUNT_HEADERS = 8;
}
