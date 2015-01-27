<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\StringReader;

/**
 * Class to get gettext strings from .mo files
 */

class Mo extends Extractor implements ExtractorInterface
{
    /**
     * Set to true if integrity checks should be skipped diring the import process, false otherwise
     * @var bool
     */
    public static $skipIntegrityChecksOnImport = true;

    const MAGIC1 = -1794895138;
    const MAGIC2 = -569244523;
    const MAGIC3 = 2500072158;

    /**
     * {@inheritDoc}
     */
    protected static function fromStringDo($string, Translations $translations, $file)
    {
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
            $stream->seekto($table_translations[$i * 2 + 2]);
            $translated = $stream->read($table_translations[$i * 2 + 1]);

            if ($original === '') {
                // Headers
                foreach (explode("\n", $translated) as $headerLine) {
                    if ($headerLine !== '') {
                        $headerChunks = preg_split('/:\s*/', $headerLine, 2);
                        $translations->setHeader($headerChunks[0], isset($headerChunks[1]) ? $headerChunks[1] : '');
                    }
                }
            } else {
                $chunks = explode("\x04", $original, 2);

                if (isset($chunks[1])) {
                    $context = $chunks[0];
                    $original = $chunks[1];
                } else {
                    $context = '';
                }

                $chunks = explode("\x00", $original, 2);

                if (isset($chunks[1])) {
                    $original = $chunks[0];
                    $plural = $chunks[1];
                } else {
                    $plural = '';
                }

                $translation = $translations->insert($context, $original, $plural);

                if ($translated !== '') {
                    if ($plural === '') {
                        $translation->setTranslation($translated);
                    } else {
                        foreach (explode("\x00", $translated) as $pluralIndex => $pluralValue) {
                            if ($pluralIndex === 0) {
                                $translation->setTranslation($pluralValue);
                            } else {
                                $translation->setPluralTranslation($pluralValue, $pluralIndex - 1);
                            }
                        }
                    }
                }
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
