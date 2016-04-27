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
        'gettext' => '__',
        '__' => '__',
        '__e' => '__',
        'ngettext' => 'n__',
        'n__' => 'n__',
        'n__e' => 'n__',
        'pgettext' => 'p__',
        'p__' => 'p__',
        'p__e' => 'p__',
        'dgettext' => 'd__',
        'd__' => 'd__',
        'd__e' => 'd__',
        'dpgettext' => 'dp__',
        'dp__' => 'dp__',
        'dp__e' => 'dp__',
        'npgettext' => 'np__',
        'np__' => 'np__',
        'np__e' => 'np__',
        'dnpgettext' => 'dnp__',
        'dnp__' => 'dnp__',
        'dnp__e' => 'dnp__',
    );

    /**
     * Set to:
     * - false to not extract comments
     * - empty string to extract all comments
     * - non-empty string to extract comments that start with that string.
     *
     * @var string|false
     */
    public static $extractComments = false;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        if ($translations === null) {
            $translations = new Translations();
        }

        $functions = new PhpFunctionsScanner($string);
        if (self::$extractComments !== false) {
            $functions->enableCommentsExtraction(self::$extractComments);
        }
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
        if (strpos($value, '\\') === false) {
            return substr($value, 1, -1);
        }

        if ($value[0] === "'") {
            return strtr(substr($value, 1, -1), array('\\\\' => '\\', '\\\'' => '\''));
        }

        $value = substr($value, 1, -1);

        return preg_replace_callback('/\\\(n|r|t|v|e|f|\$|"|\\\|x[0-9A-Fa-f]{1,2}|u{[0-9a-f]{1,6}}|[0-7]{1,3})/', function ($match) {
            switch ($match[1][0]) {
                case 'n':
                    return "\n";
                case 'r':
                    return "\r";
                case 't':
                    return "\t";
                case 'v':
                    return "\v";
                case 'e':
                    return "\e";
                case 'f':
                    return "\f";
                case '$':
                    return '$';
                case '"':
                    return '"';
                case '\\':
                    return '\\';
                case 'x':
                    return chr(hexdec(substr($match[0], 1)));
                case 'u':
                    return self::unicodeChar(hexdec(substr($match[0], 1)));
                default:
                    return chr(octdec($match[0]));
            }
        }, $value);
    }

    //http://php.net/manual/en/function.chr.php#118804
    private static function unicodeChar($dec)
    {
        if ($dec < 0x80) {
            return chr($dec);
        }

        if ($dec < 0x0800) {
            return chr(0xC0 + ($dec >> 6))
                .chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x010000) {
            return chr(0xE0 + ($dec >> 12))
                    .chr(0x80 + (($dec >> 6) & 0x3f))
                    .chr(0x80 + ($dec & 0x3f));
        }

        if ($dec < 0x200000) {
            return chr(0xF0 + ($dec >> 18))
                    .chr(0x80 + (($dec >> 12) & 0x3f))
                    .chr(0x80 + (($dec >> 6) & 0x3f))
                    .chr(0x80 + ($dec & 0x3f));
        }
    }
}
