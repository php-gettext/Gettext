<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\StringReader;

/**
 * Class to get gettext strings from .mo files
 */

class Mo extends Extractor implements ExtractorInterface
{
    const MAGIC1 = -1794895138;
    const MAGIC2 = -569244523;
    const MAGIC3 = 2500072158;

    /**
     * {@inheritDoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $stream = new StringReader($string);
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
        $tran = self::readInt($stream, $byteOrder); //offset of translation table

        $stream->seekto($originals);
        $table_originals = self::readIntArray($stream, $byteOrder, $total * 2);
        $stream->seekto($tran);
        $table_translations = self::readIntArray($stream, $byteOrder, $total * 2);

        for ($i = 0; $i < $total; $i++) {
            $stream->seekto($table_originals[$i * 2 + 2]);
            $original = $stream->read($table_originals[$i * 2 + 1]);

            if (empty($original)) {
                continue;
            }

            $stream->seekto($table_translations[$i * 2 + 2]);
            $original = explode("\000", $original, 2);
            $translated = explode("\000", $stream->read($table_translations[$i * 2 + 1]), 2);

            $plural = isset($original[1]) ? $original[1] : '';
            $pluralTranslation = isset($translated[1]) ? $translated[1] : '';

            $translation = $translations->insert(null, $original[0], $plural);
            $translation->setTranslation($translated[0]);

            if ($plural && $pluralTranslation) {
                $translation->setPluralTranslation($pluralTranslation);
            }
        }
    }

    /**
     * @param StringReader $stream
     * @param string       $byteOrder
     */
    private static function readInt(StringReader $stream, $byteOrder)
    {
        if (($read = $stream->read(4)) === false) {
            return false;
        }

        $read = unpack($byteOrder, $read);

        return array_shift($read);
    }

    /**
     * @param StringReader $stream
     * @param string       $byteOrder
     * @param int          $count
     */
    private static function readIntArray(StringReader $stream, $byteOrder, $count)
    {
        return unpack($byteOrder.$count, $stream->read(4 * $count));
    }
}
