<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\PhpFunctionsScanner;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpCode extends Extractor implements ExtractorInterface
{
    public static $functions = array(
        '__' => '__',
        '__e' => '__',
        'n__' => 'n__',
        'n__e' => 'n__',
        'p__' => 'p__',
        'p__e' => 'p__',
    );

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $functions = new PhpFunctionsScanner($string);
        $functions->saveGettextFunctions(self::$functions, $translations, $file);

        return $translations;
    }

    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function convertString($value)
    {
        if ($value[0] === "'" || strpos($value, '$') === false) {
            if (strpos($value, '\\') === false) {
                return substr($value, 1, -1);
            }

            return eval("return $value;");
        }

        $result = '';
        $value = substr($value, 1, -1);

        while (($p = strpos($value, '\\')) !== false) {
            if (!isset($value[$p + 1])) {
                break;
            }

            if ($p > 0) {
                $result .= substr($value, 0, $p);
            }

            $value = substr($value, $p + 1);
            $p = strpos($value, '$');

            if ($p === false) {
                $result .= eval('return "\\'.$value.'";');
                $value = '';
                break;
            }

            if ($p === 0) {
                $result .= '$';
                $value = substr($value, 1);
            } else {
                $result .= eval('return "\\'.substr($value, 0, $p).'";');
                $value = substr($value, $p);
            }
        }

        return $result.$value;
    }
}
