<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

use Gettext\Translation;

/**
 * Trait with common gettext function handlers
 */
trait FunctionsHandlersTrait
{
    protected function gettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 1)) {
            return null;
        }
        list($original) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation(null, null, $original)
        );
    }

    protected function ngettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        list($original, $plural) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation(null, null, $original, $plural)
        );
    }

    protected function pgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        list($context, $original) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation(null, $context, $original)
        );
    }

    protected function dgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        list($domain, $original) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation($domain, null, $original)
        );
    }

    protected function dpgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        list($domain, $context, $original) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation($domain, $context, $original)
        );
    }

    protected function npgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        list($context, $original, $plural) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation(null, $context, $original, $plural)
        );
    }

    protected function dngettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        list($domain, $original, $plural) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation($domain, null, $original, $plural)
        );
    }

    protected function dnpgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 4)) {
            return null;
        }
        list($domain, $context, $original, $plural) = $function->getArguments();

        return $this->addComments(
            $function,
            $this->saveTranslation($domain, $context, $original, $plural)
        );
    }

    abstract protected function addComments(ParsedFunction $function, ?Translation $translation): ?Translation;

    abstract protected function checkFunction(ParsedFunction $function, int $minLength): bool;

    abstract protected function saveTranslation(
        ?string $domain,
        ?string $context,
        string $original,
        string $plural = null
    ): ?Translation;
}
