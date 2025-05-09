<?php
declare(strict_types = 1);

namespace Gettext\Scanner;

use Gettext\Translation;

/**
 * Trait with common gettext function handlers
 *
 * @phpstan-ignore trait.unused
 */
trait FunctionsHandlersTrait
{
    protected function gettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 1)) {
            return null;
        }
        [$original] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation(null, null, $original)
        );

        return $this->addFlags($function, $translation);
    }

    protected function ngettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        [$original, $plural] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation(null, null, $original, $plural)
        );

        return $this->addFlags($function, $translation);
    }

    protected function pgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        [$context, $original] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation(null, $context, $original)
        );

        return $this->addFlags($function, $translation);
    }

    protected function dgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 2)) {
            return null;
        }
        [$domain, $original] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation($domain, null, $original)
        );

        return $this->addFlags($function, $translation);
    }

    protected function dpgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        [$domain, $context, $original] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation($domain, $context, $original)
        );

        return $this->addFlags($function, $translation);
    }

    protected function npgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        [$context, $original, $plural] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation(null, $context, $original, $plural)
        );

        return $this->addFlags($function, $translation);
    }

    protected function dngettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 3)) {
            return null;
        }
        [$domain, $original, $plural] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation($domain, null, $original, $plural)
        );

        return $this->addFlags($function, $translation);
    }

    protected function dnpgettext(ParsedFunction $function): ?Translation
    {
        if (!$this->checkFunction($function, 4)) {
            return null;
        }
        [$domain, $context, $original, $plural] = $function->getArguments();

        $translation = $this->addComments(
            $function,
            $this->saveTranslation($domain, $context, $original, $plural)
        );

        return $this->addFlags($function, $translation);
    }

    abstract protected function addComments(ParsedFunction $function, ?Translation $translation): ?Translation;

    abstract protected function addFlags(ParsedFunction $function, ?Translation $translation): ?Translation;

    abstract protected function checkFunction(ParsedFunction $function, int $minLength): bool;

    abstract protected function saveTranslation(
        ?string $domain,
        ?string $context,
        string $original,
        ?string $plural = null
    ): ?Translation;
}
