<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Translation;

/**
 * Class to get gettext strings from php files returning arrays
 */
class Po extends Extractor implements ExtractorInterface
{
    /**
     * Parses a .po file and append the translations found in the Translations instance
     *
     * {@inheritDoc}
     */
    protected static function fromStringDo($string, Translations $translations, $file)
    {
        $lines = explode("\n", $string);
        $i = 1;
        $currentHeader = null;

        while ((isset($lines[++$i]) && ($line = trim($lines[$i])) !== '')) {
            $line = self::clean($line);

            if (self::isHeaderDefinition($line)) {
                $header = array_map('trim', explode(':', $line, 2));
                $currentHeader = $header[0];
                $translations->setHeader($currentHeader, $header[1]);
            } else {
                $entry = $translations->getHeader($currentHeader);
                $translations->setHeader($currentHeader, $entry.$line);
            }
        }

        $translation = new Translation();

        for ($n = count($lines); $i < $n; $i++) {
            $line = trim($lines[$i]);

            $line = self::fixMultiLines($line, $lines, $i);

            if ($line === '') {
                if ($translation->hasOriginal()) {
                    $translations[] = $translation;
                    $translation = new Translation();
                }
                continue;
            }

            $splitLine = preg_split('/\s+/', $line, 2);
            $key = $splitLine[0];
            $data = isset($splitLine[1]) ? $splitLine[1] : '';
            $append = null;

            switch ($key) {
                case '#':
                    $translation->addComment($data);
                    $append = null;
                    break;

                case '#.':
                    $translation->addExtractedComment($data);
                    $append = null;
                    break;

                case '#,':
                    foreach (array_map('trim', explode(',', trim($data))) as $value) {
                        $translation->addFlag($value);
                    }
                    $append = null;
                    break;

                case '#:':
                    foreach (preg_split('/\s+/', trim($data)) as $value) {
                        if (preg_match('/^(.+)(:(\d*))?$/U', $value, $matches)) {
                            $translation->addReference($matches[1], isset($matches[3]) ? $matches[3] : null);
                        }
                    }
                    $append = null;
                    break;

                case 'msgctxt':
                    $translation->setContext(self::clean($data));
                    $append = 'Context';
                    break;

                case 'msgid':
                    $translation->setOriginal(self::clean($data));
                    $append = 'Original';
                    break;

                case 'msgid_plural':
                    $translation->setPlural(self::clean($data));
                    $append = 'Plural';
                    break;

                case 'msgstr':
                case 'msgstr[0]':
                    $translation->setTranslation(self::clean($data));
                    $append = 'Translation';
                    break;

                case 'msgstr[1]':
                    $translation->setPluralTranslation(self::clean($data), 0);
                    $append = 'PluralTranslation';
                    break;

                default:
                    if (strpos($key, 'msgstr[') === 0) {
                        $translation->setPluralTranslation(self::clean($data), intval(substr($key, 7, -1)) - 1);
                        $append = 'PluralTranslation';
                        break;
                    }

                    if (isset($append)) {
                        if ($append === 'PluralTranslation') {
                            $key = count($translation->getPluralTranslation()) - 1;
                            $translation->setPluralTranslation($translation->getPluralTranslation($key)."\n".self::clean($data), $key);
                            break;
                        }

                        $getMethod = 'get'.$append;
                        $setMethod = 'set'.$append;
                        $translation->$setMethod($translation->$getMethod()."\n".self::clean($data));
                    }
                    break;
            }
        }

        if ($translation->hasOriginal() && !in_array($translation, iterator_to_array($translations))) {
            $translations[] = $translation;
        }
    }

    /**
     * Checks if it is a header definition line. Useful for distguishing between header definitions
     * and possible continuations of a header entry
     *
     * @param  string  $line Line to parse
     * @return boolean
     */
    private static function isHeaderDefinition($line)
    {
        return (bool) preg_match('/^[\w-]+:/', $line);
    }

    /**
     * Cleans the strings. Removes quotes and "\n", etc
     *
     * @param string $str
     *
     * @return string
     */
    private static function clean($str)
    {
        if ($str[0] === '"') {
            $str = substr($str, 1, -1);
        }

        return str_replace(array('\\n', '\\"'), array("\n", '"'), $str);
    }

    /**
     * Gets one strings from multiline strings
     *
     * @param string  $line
     * @param array   $lines
     * @param integer &$i
     *
     * @return string
     */
    private static function fixMultiLines($line, array $lines, &$i)
    {
        for ($j = $i; $j<count($lines); $j++) {
            if (substr($line, -1, 1) == '"'
                && isset($lines[$j+1])
                && substr(trim($lines[$j+1]), 0, 1) == '"'
            ) {
                $line = substr($line, 0, -1).substr(trim($lines[$j+1]), 1);
            } else {
                $i = $j;
                break;
            }
        }

        return $line;
    }
}
