<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

use Exception;
use Gettext\Translation;
use Gettext\Translations;

/**
 * Class to scan files with code and get gettext translations.
 */
abstract class CodeScanner extends Scanner
{
    protected $functions = [
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
        'noop' => 'gettext',
        'noop__' => 'gettext',
    ];

    public function scanString(string $string, string $filename = null): void
    {
        $functionsScanner = $this->getFunctionsScanner();
        $functions = $functionsScanner->scan($string, $filename);

        foreach ($functions as $function) {
            $this->handleFunction($function);
        }
    }

    abstract public function getFunctionsScanner(): FunctionsScannerInterface;

    protected function handleFunction(ParsedFunction $function)
    {
        $name = $function->getName();
        $handler = $this->functions[$name] ?? null;

        if (is_null($handler)) {
            return;
        }

        $translation = call_user_func([$this, $handler], $function);

        if ($translation) {
            $translation->getReferences()->add($function->getFilename(), $function->getLine());
        }
    }

    protected function gettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 1);
        list($original) = $function->getArguments();

        return $this->saveTranslation(null, null, $original);
    }

    protected function ngettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 2);
        list($original, $plural) = $function->getArguments();

        return $this->saveTranslation(null, null, $original, $plural);
    }

    protected function pgettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 2);
        list($context, $original) = $function->getArguments();

        return $this->saveTranslation(null, $context, $original);
    }

    protected function dgettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 2);
        list($domain, $original) = $function->getArguments();

        return $this->saveTranslation($domain, null, $original);
    }

    protected function dpgettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 3);
        list($domain, $context, $original) = $function->getArguments();

        return $this->saveTranslation($domain, $context, $original);
    }

    protected function npgettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 3);
        list($context, $original, $plural) = $function->getArguments();

        return $this->saveTranslation(null, $context, $original, $plural);
    }

    protected function dngettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 3);
        list($domain, $original, $plural) = $function->getArguments();

        return $this->saveTranslation($domain, null, $original, $plural);
    }

    protected function dnpgettext(ParsedFunction $function): ?Translation
    {
        static::checkArguments($function, 4);
        list($domain, $context, $original, $plural) = $function->getArguments();

        return $this->saveTranslation($domain, $context, $original, $plural);
    }

    protected static function checkArguments(ParsedFunction $function, int $minLength)
    {
        if ($function->countArguments() < $minLength) {
            throw new Exception(
                sprintf(
                    'Invalid gettext function in %s:%d. At least %d arguments are required',
                    $function->getFilename(),
                    $function->getLine(),
                    $minLength
                )
            );
        }

        $arguments = array_slice($function->getArguments(), 0, $minLength);

        if (in_array(null, $arguments, true)) {
            throw new Exception(
                sprintf(
                    'Invalid gettext function in %s:%d. Some required arguments are not valid',
                    $function->getFilename(),
                    $function->getLine()
                )
            );
        }
    }
}
