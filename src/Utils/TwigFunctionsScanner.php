<?php

/**
 * Copyright (C) 2018, raphael.droz@gmail.com
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */

namespace Gettext\Utils;

use Gettext\Utils\PhpFunctionsScanner;

/**
 * This is a function scanner for Twig templates. The initial string (whether template text or filename) is parsed.
 * Then getFunctions() recursively traverses nodes.
 * Nodes expressing a function call to one of the known i18n functions are extracted.
 * Their list is passed to constructor $func_names argument.
 */
class TwigFunctionsScanner extends PhpFunctionsScanner {

    private $filename = '';
    private $content = null;
    private $twig = null;
    private $function_names = [];

    public function __construct(array $file, $twig, $func_names) {
        if (isset($file['filename'])) {
            $this->filename = $filename = $file['filename'];
            $this->content = $content = file_get_contents($filename);
        }
        elseif (isset($file['content'])) {
            $this->filename = $filename = '';
            $this->content = $content = $file['content'];
        }
        else {
            return NULL;
        }
        $this->function_names = $func_names;
        $this->twig = $twig;
        $this->tokens = $twig->parse($twig->tokenize(new \Twig_Source($content, $filename)));
    }

    /**
     * A pseudo-generator to extract twig nodes corresponding to i18n function calls.
     * @param array $constants Unused yet.
     * @return array List of functions arguments/line-number compatible with PhpFunctionsScanner.
     */
    public function getFunctions(array $constants = []) {
        return self::_get_gettext_functions($this->tokens);
    }

    private function is_gettext_function($obj ) {
        return ($obj instanceof \Twig_Node_Expression_Function && in_array($obj->getAttribute('name'), $this->function_names, TRUE));
    }

    private function _get_gettext_functions($tokens) {
        if (is_array($tokens)) {
            $functions = [];
            foreach($tokens as $v) {
                $functions = array_merge($functions, self::_get_gettext_functions($v));
            }
            return $functions;
        }

        $value = $tokens;
        if ($this->is_gettext_function($value)) {
            $arguments_obj = (array)$value->getNode('arguments')->getIterator();
            $name = $value->getAttribute('name');
            $line = $value->getTemplateLine();

            // basic verification of node arguments
            if (count($arguments_obj) < 2) {
                // can't have a text domain... ToDo
            }
            if (! ($arguments_obj[0] instanceof \Twig_Node_Expression_Constant)) {
                printf(STDERR, "Translation expression does not contains constant expression" . PHP_EOL);
                printf(STDERR, print_r($arguments_obj, TRUE));
                return [];
            }
            if (FALSE && ! ($arguments_obj[1] instanceof \Twig_Node_Expression_Constant)) {
                printf(STDERR, "Translation expression does not contains constant text domain" . PHP_EOL);
                printf(STDERR, print_r($arguments_obj, TRUE));
                return [];
            }

            $arguments = array_map(function($obj) use($name) {
                if ($name == '_n' && $obj instanceof \Twig_Node_Expression_GetAttr) {
                    return "count";
                } else {
                    return $obj->getAttribute('value');
                }
            }, $arguments_obj);

            return [ [ $name, $line, $arguments ] ];
        }

        $functions = [];
        foreach($tokens->getIterator() as $v) {
            $functions = array_merge($functions, self::_get_gettext_functions($v));
        }
        return $functions;
    }

    // This is bundled as-is from
    // https://github.com/wp-cli/i18n-command/blob/master/src/PhpFunctionsScanner.php#L12
    public function saveGettextFunctions($translations, $options) {
        $functions = $options['functions'];
        $file      = $options['file'];

        $f = $this->getFunctions($options['constants']);
        foreach ($f as $function) {
            list($name, $line, $args) = $function;

            if (! isset($functions[ $name ])) {
                continue;
            }

            $context = $plural = null;

            switch ($functions[ $name ]) {
            case 'text_domain':
            case 'gettext':
                list($original, $domain) = array_pad($args, 2, null);
                break;

            case 'text_context_domain':
                list($original, $context, $domain) = array_pad($args, 3, null);
                break;

            case 'single_plural_number_domain':
                list($original, $plural, $number, $domain) = array_pad($args, 4, null);
                break;

            case 'single_plural_number_context_domain':
                list($original, $plural, $number, $context, $domain) = array_pad($args, 5, null);
                break;

            case 'single_plural_domain':
                list($original, $plural, $domain) = array_pad($args, 3, null);
                break;

            case 'single_plural_context_domain':
                list($original, $plural, $context, $domain) = array_pad($args, 4, null);
                break;

            default:
                // Should never happen.
                \WP_CLI::error(sprintf("Internal error: unknown function map '%s' for '%s'.", $functions[ $name ], $name));
            }

            if ((string) $original !== '' && ($domain === $translations->getDomain() || null === $translations->getDomain())) {
                $translation = $translations->insert($context, $original, $plural);
                $translation = $translation->addReference($file, $line);

                if (isset($function[3])) {
                    foreach ($function[3] as $extractedComment) {
                        $translation = $translation->addExtractedComment($extractedComment);
                    }
                }
            }
        }
    }
}
