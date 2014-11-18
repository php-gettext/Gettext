<?php
namespace Gettext\Generators;

use Gettext\Entries;

abstract class Generator
{
	/**
	 * Saves the entries in a file
	 * 
	 * @param Entries $entries
	 * @param string  $file
	 * 
	 * @return boolean
	 */
    public static function toFile(Entries $entries, $file)
    {
        $content = static::toString($entries);

        if (file_put_contents($file, $content) === false) {
            return false;
        }

        return true;
    }

    /**
	 * Generates a string with the entries ready to save in a file
	 * 
	 * @param Entries $entries
	 * 
	 * @return string
	 */
    abstract static function toString(Entries $entries);
}
