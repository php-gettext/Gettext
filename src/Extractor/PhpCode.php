<?php

namespace Gettext\Extractor;

use Exception;
use Gettext\Translations;
use Gettext\Translation;
use Gettext\Utils\FunctionsScanner;

/**
 * Class to get gettext strings from php files returning arrays.
 */
class PhpCode extends Extractor
{
    public static $options = [
        // - false: to not extract comments
        // - empty string: to extract all comments
        // - non-empty string: to extract comments that start with that string
        // - array with strings to extract comments format.
        'extractComments' => false,

        'constants' => [],

        'functions' => [
            'gettext' => 'gettext',
            '__' => 'gettext',
            'ngettext' => 'ngettext',
            'n__' => 'ngettext',
            'pgettext' => 'pgettext',
            'p__' => 'pgettext',
            'dgettext' => 'dgettext',
            'd__' => 'dgettext',
            'dngettext' => 'dngettext',
            'dn__' => 'dngettext',
            'dpgettext' => 'dpgettext',
            'dp__' => 'dpgettext',
            'npgettext' => 'npgettext',
            'np__' => 'npgettext',
            'dnpgettext' => 'dnpgettext',
            'dnp__' => 'dnpgettext',
            'noop' => 'noop',
            'noop__' => 'noop',
        ],
    ];

    public function extractfromString(string $string, string $filename = null): self
    {
        $functionsExtractor = $this->getFunctionsExtractor();
        $functions = $functionsExtractor->extractFunctions($string, $filename);

        foreach ($functions as $function) {
            $this->handleFunction($function);
        }
    }

    protected function getFunctionsExtractor(): FunctionsExtractorInterface
    {
        
    }

    protected function handleFunction(ParsedFunction $function)
    {
        $name = $function->getName();
        $handler = $this->options['functions'][$name] ?? null;

        if (is_null($handler)) {
            return;
        }

        $translation = call_user_func([$this, $handler], $function);

        if ($translation) {
            $translation->getReferences()->add($function->getFilename(), $function->getLine());
        }
    }

    protected function gettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 1);
        list($original) = $function->getArguments();

        return $this->saveTranslation(null, null, $original);
    }

    protected function ngettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 2);
        list($original, $plural) = $function->getArguments();
        
        return $this->saveTranslation(null, null, $original, $plural);
    }

    protected function pgettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 2);
        list($context, $original) = $function->getArguments();
        
        return $this->saveTranslation(null, $context, $original);
    }

    protected function dgettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 2);
        list($domain, $original) = $function->getArguments();
        
        return $this->saveTranslation($domain, null, $original);
    }

    protected function dpgettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 3);
        list($domain, $context, $original) = $function->getArguments();
        
        return $this->saveTranslation($domain, $context, $original);
    }

    protected function npgettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 3);
        list($context, $original, $plural) = $function->getArguments();
        
        return $this->saveTranslation(null, $context, $original, $plural);
    }

    protected function dngettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 3);
        list($domain, $original, $plural) = $function->getArguments();
        
        return $this->saveTranslation($domain, null, $original, $plural);
    }

    protected function dnpgettext(ParsedFunction $function): Translation
    {
        static::checkArguments($function, 4);
        list($domain, $context, $original, $plural) = $function->getArguments();
        
        return $this->saveTranslation($domain, $context, $original, $plural);
    }

    protected static function checkArguments(ParsedFunction $function, int $minLength)
    {
        if ($function->countArguments() >= $minLength) {
            return;
        }

        throw new Exception(
            sprintf(
                'Invalid gettext function in %s (line: %d). At least %d arguments are required', 
                $function->getFilename(), 
                $function->getLine(), 
                $minLength
            )
        );
    }
}
