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

            $domain = $context = $original = $plural = $translation = null;

            switch ($functions[$name]) {
                case 'gettext':
                    if (!isset($args[0])) {
                        continue 2;
                    }

                    $original = $args[0];
                    break;

                case 'ngettext':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($original, $plural) = $args;
                    break;

                case 'pgettext':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($context, $original) = $args;
                    break;

                case 'dgettext':
                    if (!isset($args[1])) {
                        continue 2;
                    }

                    list($domain, $original) = $args;
                    break;

                case 'dpgettext':
                    if (!isset($args[2])) {
                        continue 2;
                    }

                    list($domain, $context, $original) = $args;
                    break;

                case 'npgettext':
                    if (!isset($args[2])) {
                        continue 2;
                    }

                    list($context, $original, $plural) = $args;
                    break;

                case 'dnpgettext':
                    if (!isset($args[4])) {
                        continue 2;
                    }

                    list($domain, $context, $original, $plural) = $args;
                    break;

                default:
                    throw new Exception(sprintf('Not valid function %s', $functions[$name]));
            }

            if ((string) $original !== '' && ($domain === null || $domain === $translations->getDomain())) {
                $translation = $translations->insert($context, $original, $plural);
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
