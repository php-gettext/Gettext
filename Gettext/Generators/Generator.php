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
    public static function generateFile(Entries $entries, $file)
    {
        $content = static::generate($entries, true);

        if (file_put_contents($file, $content) === false) {
            return false;
        }

        return true;
    }
}
