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
        'p__e' => 'p__'
    );


    /**
     * {@inheritDoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        $functions = new PhpFunctionsScanner($string);

        foreach ($functions->getFunctions() as $function) {
            list($name, $line, $args) = $function;

            if (!isset(self::$functions[$name])) {
                continue;
            }

            switch (self::$functions[$name]) {
                case '__':
                    if (!isset($args[0])) {
                        continue 2;
                    }
                    $original = $args[0];
                    $translation = $translations->find('', $original) ?: $translations->insert('', $original);
                    break;

                case 'n__':
                    if (!isset($args[1])) {
                        continue 2;
                    }
                    $original = $args[0];
                    $plural = $args[1];
                    $translation = $translations->find('', $original, $plural) ?: $translations->insert('', $original, $plural);
                    break;

                case 'p__':
                    if (!isset($args[1])) {
                        continue 2;
                    }
                    $context = $args[0];
                    $original = $args[1];
                    $translation = $translations->find($context, $original) ?: $translations->insert($context, $original);
                    break;

                default:
                    throw new \Exception('Not valid functions');
            }

            $translation->addReference($file, $line);
        }
    }
}
