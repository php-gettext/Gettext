<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Jed extends Generator
{
	/**
	 * {@parentDoc}
	 */
    public static function toString(Entries $entries)
    {
        $array = PhpArray::generateArray($entries);

        return json_encode($array);
    }
}
