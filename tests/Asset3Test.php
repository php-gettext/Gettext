<?php

namespace Gettext\Tests;

class Asset3Test extends Asset1Test
{
    protected static $directory = '3';
    protected static $input = 'Po';

    const COUNT_TRANSLATIONS = 13;
    const COUNT_EMPTY_TRANSLATIONS = 3;
    const COUNT_HEADERS = 13;
}
