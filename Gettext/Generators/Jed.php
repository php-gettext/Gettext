<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Jed extends Generator
{
	/**
	 * Generates a string with the entries ready to save in a file
	 * 
	 * @param Entries $entries
	 * @param boolean $encoded True to encode to javascript, false to return an array
	 * 
	 * @return array|string
	 */
    public static function generate(Entries $entries, $encoded = true)
    {
        $translations = PhpArray::generate($entries);

        return $encoded ? json_encode($translations) : $translations;
    }
}
