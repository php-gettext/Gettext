<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Mo extends Generator
{
    /**
     * Generates a string with the entries ready to save in a file
     * 
     * @param Entries $entries
     * 
     * @return string
     */
    public static function generate(Entries $entries)
    {
        $translations = array();

        foreach ($entries as $translation) {
            if ($translation->hasTranslation()) {
                $translations[$translation->getOriginal()] = $translation;
            }
        }

        ksort($translations, SORT_STRING);

        $offsets = array();
        $ids = '';
        $strings = '';

        foreach ($translations as $translation) {
            $id = $translation->getOriginal();

            if ($translation->hasPlural()) {
                $id .= "\x00".$translation->getPlural();
            }

            if ($translation->hasContext()) {
                $id = $translation->getContext()."\x04".$id;
            }

            //Plural msgstrs are NUL-separated
            $msgstrs = array_merge(array($translation->getTranslation()), $translation->getPluralTranslation());
            $str = str_replace("\n", "\x00", implode("\x00", $msgstrs));

            $offsets[] = array(strlen($ids), strlen($id), strlen($strings), strlen($str));

            //plural msgids are not stored (?)
            $ids .= $id . "\x00";
            $strings .= $str . "\x00";
        }

        $key_start = 7 * 4 + count($translations) * 4 * 4;
        $value_start = $key_start + strlen($ids);
        $key_offsets = array();
        $value_offsets = array();

        //Calculate
        foreach ($offsets as $v) {
            list ($o1, $l1, $o2, $l2) = $v;

            $key_offsets[] = $l1;
            $key_offsets[] = $o1 + $key_start;
            $value_offsets[] = $l2;
            $value_offsets[] = $o2 + $value_start;
        }

        $offsets = array_merge($key_offsets, $value_offsets);

        //Generate binary data
        $mo = pack('Iiiiiii', 0x950412de, 0, count($translations), 7 * 4, 7 * 4 + count($translations) * 8, 0, $key_start);

        foreach ($offsets as $offset) {
            $mo .= pack('i', $offset);
        }

        return $mo.$ids.$strings;
    }
}
