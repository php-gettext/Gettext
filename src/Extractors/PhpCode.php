<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Gettext\Utils\PhpFunctionsScanner;

/**
 * Class to get gettext strings from php files returning arrays
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
     * {@inheritDoc}
     */
    protected static function fromStringDo($string, Translations $translations, $file)
    {
        $functions = new PhpFunctionsScanner($string);
        $functions->saveGettextFunctions(self::$functions, $translations, $file);
    }
}
