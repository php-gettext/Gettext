<?php
namespace Gettext\Utils;

use Exception;
use Gettext\Translations;

abstract class FunctionsScanner
{
    /**
     * Scan and returns the functions and the arguments
     *
     * @return array
     */
    abstract public function getFunctions();

    /**
     * Search for specific functions and create translations
     *
     * @param array        $functions    The gettext functions to search
     * @param Translations $translations The translations instance where save the values
     * @param string       $file         The filename used to the reference
     */
    public function saveGettextFunctions(array $functions, Translations $translations, $file = '')
    {
        foreach ($this->getFunctions() as $function) {
            list($name, $line, $args) = $function;

            if (!isset($functions[$name])) {
                continue;
            }

            switch ($functions[$name]) {
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
                    throw new Exception('Not valid functions');
            }

            $translation->addReference($file, $line);
        }
    }
}
