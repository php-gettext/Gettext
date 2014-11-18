<?php
namespace Gettext\Extractors;

use Gettext\Entries;

abstract class Extractor
{
    /**
     * Extract the entries from a file
     * 
     * @param array|string $file    A path of a file or files
     * @param null|Entries $entries The entries instance to append the new translations.
     * 
     * @return Entries
     */
    public static function fromFile($file, Entries $entries = null)
    {
        if ($entries === null) {
            $entries = new Entries;
        }

        foreach (self::getFiles($file) as $file) {
            self::fromString(file_get_contents($file), $entries);
        }

        return $entries;
    }


    /**
     * Parses a string and append the translations found in the Entries instance
     * 
     * @param string       $string
     * @param Entries|null $entries
     *
     * @return Entries
     */
    abstract public static function fromString($string, Entries $entries = null);


    /**
     * Checks and returns all files
     * 
     * @param string|array $file The file/s
     * 
     * @return array The file paths
     */
    protected static function getFiles($file)
    {
        if (empty($file)) {
            throw new \InvalidArgumentException('There is not any file defined');
        }

        if (is_string($file)) {
            if (!is_file($file)) {
                throw new \InvalidArgumentException("'$file' is not a valid file");
            }

            if (!is_readable($file)) {
                throw new \InvalidArgumentException("'$file' is not a readable file");
            }

            return array($file);
        }

        if (is_array($file)) {
            $files = array();

            foreach ($file as $f) {
                $files = array_merge($files, self::getFiles($f));
            }

            return $files;
        }

        throw new \InvalidArgumentException('The first argumet must be string or array');
    }
}
