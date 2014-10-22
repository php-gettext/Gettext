<?php
namespace Gettext\Extractors;

use Gettext\Entries;

/**
 * Class to get gettext strings from mo files
 */

class Mo extends Extractor
{
    const MAGIC1 = -1794895138;
    const MAGIC2 = -569244523;
    const MAGIC3 = 2500072158;


    /**
     * Parses a .mo file and append the translations found in the Entries instance
     * 
     * @param string  $file
     * @param Entries $entries
     */
    public static function parse($file, Entries $entries)
    {
        $stream = new CachedFileReader($file);

        if (!$stream || isset($stream->error)) {
            return false;
        }

        $magic = self::readInt($stream, 'V');

        if (($magic === self::MAGIC1) || ($magic === self::MAGIC3)) { //to make sure it works for 64-bit platforms
            $byteOrder = 'V'; //low endian
        } elseif ($magic === (self::MAGIC2 & 0xFFFFFFFF)) {
            $byteOrder = 'N'; //big endian
        } else {
            throw new \Exception('Not MO file');
        }

        self::readInt($stream, $byteOrder);

        $total = self::readInt($stream, $byteOrder); //total string count
        $originals = self::readInt($stream, $byteOrder); //offset of original table
        $translations = self::readInt($stream, $byteOrder); //offset of translation table

        $stream->seekto($originals);
        $table_originals = self::readIntArray($stream, $byteOrder, $total * 2);
        $stream->seekto($translations);
        $table_translations = self::readIntArray($stream, $byteOrder, $total * 2);

        for ($i = 0; $i < $total; $i++) {
            $stream->seekto($table_originals[$i * 2 + 2]);
            $original = $stream->read($table_originals[$i * 2 + 1]);

            if ($original) {
                $stream->seekto($table_translations[$i * 2 + 2]);
                $original = explode("\000", $original, 2);
                $translated = explode("\000",$stream->read($table_translations[$i * 2 + 1]), 2);

                $plural = isset($original[1]) ? $original[1] : '';
                $pluralTranslation = isset($translated[1]) ? $translated[1] : '';

                $translation = $entries->insert(null, $original[0], $plural);
                $translation->setTranslation($translated[0]);

                if ($plural && $pluralTranslation) {
                    $translation->setPluralTranslation($pluralTranslation);
                }
            }
        }
    }

    /**
     * @param CachedFileReader $stream
     * @param string           $byteOrder
     */
    private static function readInt($stream, $byteOrder)
    {
        if (($read = $stream->read(4)) === false) {
            return false;
        }

        $read = unpack($byteOrder, $read);

        return array_shift($read);
    }

    /**
     * @param CachedFileReader $stream
     * @param string           $byteOrder
     * @param int              $count
     */
    private static function readIntArray($stream, $byteOrder, $count)
    {
        return unpack($byteOrder.$count, $stream->read(4 * $count));
    }
}

class CachedFileReader
{
    public $pos;
    public $str;
    public $strlen;

    /**
     * @param string  $filename
     */
    public function __construct($filename)
    {
        if (is_file($filename)) {
            $length = filesize($filename);
            $fd = fopen($filename,'rb');

            if (!$fd) {
                throw new \Exception("Cannot read the file '$filename', probably permissions");
            }

            $this->str = fread($fd, $length);
            $this->strlen = strlen($this->str);

            fclose($fd);
        } else {
            throw new \Exception("The file '$filename' does not exists");
        }
    }

    public function read($bytes)
    {
        $data = substr($this->str, $this->pos, $bytes);

        $this->seekto($this->pos + $bytes);

        return $data;
    }

    public function seekto($pos)
    {
        $this->pos = ($this->strlen < $pos) ? $this->strlen : $pos;

        return $this->pos;
    }
}
