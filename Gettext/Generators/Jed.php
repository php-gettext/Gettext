<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Jed extends Generator
{
    public static function generate(Entries $entries, $encoded = true)
    {
        $translations = PhpArray::generate($entries);

        return $encoded ? json_encode($translations) : $translations;
    }
}
