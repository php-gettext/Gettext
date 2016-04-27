<?php

namespace Gettext\Utils;

use Exception;
use Gettext\Translations;

abstract class FunctionsScanner
{
    /**
     * Scan and returns the functions and the arguments.
     *
     * @return array
     */
    abstract public function getFunctions();

    /**
     * Search for specific functions and create translations.
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

            $translation = null;

            switch ($functions[$name]) {
                case '__':
                    if (!isset($args[0])) {
                        continue 2;
                    }

                    $original = $args[0];

                    if ($original !== '') {
                        $translation = $translations->insert('', $original);
                    }
                    break;

                case 'n__':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($original, $plural) = $args;

                    if ($original !== '') {
                        $translation = $translations->insert('', $original, $plural);
                    }
                    break;

                case 'p__':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($context, $original) = $args;

                    if ($original !== '') {
                        $translation = $translations->insert($context, $original);
                    }
                    break;

                case 'd__':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($domain, $original) = $args;

                    if ($original !== '' && $domain === $translations->getDomain()) {
                        $translation = $translations->insert('', $original);
                    }
                    break;

                case 'dp__':
                    if (!isset($args[2])) {
                        continue 2;
                    }

                    list($domain, $context, $original) = $args;

                    if ($original !== '' && $domain === $translations->getDomain()) {
                        $translation = $translations->insert($context, $original);
                    }
                    break;

                case 'np__':
                    if (!isset($args[2])) {
                        continue 2;
                    }

                    list($context, $original, $plural) = $args;

                    if ($original !== '') {
                        $translation = $translations->insert($context, $original, $plural);
                    }
                    break;

                case 'dnp__':
                    if (!isset($args[4])) {
                        continue 2;
                    }

                    list($domain, $context, $original, $plural) = $args;

                    if ($original !== '' && $domain === $translations->getDomain()) {
                        $translation = $translations->insert($context, $original, $plural);
                    }
                    break;

                default:
                    throw new Exception('Not valid functions');
            }

            if (isset($translation)) {
                $translation->addReference($file, $line);
                if (isset($function[3])) {
                    foreach ($function[3] as $extractedComment) {
                        $translation->addExtractedComment($extractedComment);
                    }
                }
            }
        }
    }
}
