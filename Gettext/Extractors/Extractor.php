<?php
namespace Gettext\Extractors;

use Gettext\Entries;

abstract class Extractor
{
    /**
     * Extract the entries from a file
     * 
     * @param array|string $file    A path of a file or folder
     * @param null|Entries $entries The entries instance to append the new translations.
     * 
     * @return Entries
     */
    public static function extract($file, Entries $entries = null)
    {
        if (empty($file)) {
            throw new \InvalidArgumentException('There is not a file defined');
        }

        if ($entries === null) {
            $entries = new Entries;
        }

        if (($file = self::resolve($file)) === false) {
            return false;
        }

        if (is_array($file)) {
            foreach ($file as $f) {
                static::extract($f, $entries);
            }

            return $entries;
        }

        if (!is_readable($file)) {
            throw new \InvalidArgumentException("'$file' is not a readable file");
        }

        static::parse($file, $entries);

        return $entries;
    }


    /**
     * Search the files in a folder
     * 
     * @param string $path The file/folder path
     * 
     * @return string|array The file path or an array of file paths
     */
    private static function resolve($path)
    {
        if (is_string($path)) {
            if (is_file($path)) {
                return $path;
            }

            if (is_dir($path)) {
                $files = array();

                $directory = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
                $iterator = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::LEAVES_ONLY);

                foreach ($iterator as $fileinfo) {
                    $name = $fileinfo->getPathname();

                    if (strpos($name, '/.') === false) {
                        $files[] = $name;
                    }
                }

                return $files;
            }

            throw new \InvalidArgumentException("'$path' is not a valid file or folder");
        }

        if (is_array($path)) {
            $files = array();

            foreach ($path as $file) {
                $file = self::resolve($file);

                if (is_array($file)) {
                    $files = array_merge($files, $file);
                } else {
                    $files[] = $file;
                }
            }

            return $files;
        }

        throw new \InvalidArgumentException('The first argumet must be string or array');
    }
}
