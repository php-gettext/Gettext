<?php

namespace Gettext\Extractors;

use Exception;
use InvalidArgumentException;
use Gettext\Translations;

abstract class Extractor
{
    /**
     * Extract the translations from a file.
     *
     * @param array|string      $file         A path of a file or files
     * @param null|Translations $translations The translations instance to append the new translations.
     *
     * @return Translations
     */
    public static function fromFile($file, Translations $translations = null)
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        foreach (self::getFiles($file) as $file) {
            static::fromString(self::readFile($file), $translations, $file);
        }

        return $translations;
    }

    /**
     * Checks and returns all files.
     *
     * @param string|array $file The file/s
     *
     * @return array The file paths
     */
    protected static function getFiles($file)
    {
        if (empty($file)) {
            throw new InvalidArgumentException('There is not any file defined');
        }

        if (is_string($file)) {
            if (!is_file($file)) {
                throw new InvalidArgumentException("'$file' is not a valid file");
            }

            if (!is_readable($file)) {
                throw new InvalidArgumentException("'$file' is not a readable file");
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

        throw new InvalidArgumentException('The first argumet must be string or array');
    }

    /**
     * Reads and returns the content of a file.
     *
     * @param string $file
     *
     * @return string
     */
    protected static function readFile($file)
    {
        $length = filesize($file);

        if (!($fd = fopen($file, 'rb'))) {
            throw new Exception("Cannot read the file '$file', probably permissions");
        }

        $content = $length ? fread($fd, $length) : '';
        fclose($fd);

        return $content;
    }
}
