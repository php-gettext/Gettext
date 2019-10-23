<?php

namespace Gettext;

use Gettext\Languages\Language;
use BadMethodCallException;
use InvalidArgumentException;
use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Class to manage a collection of translations under the same domain.
 */
class Translations implements Countable, IteratorAggregate
{
    protected $translations = [];
    protected $headers;

    public function __construct(string $domain = null)
    {
        $this->headers = new Headers();

        if (isset($domain)) {
            $this->setDomain($domain);
        }
    }

    public function __clone()
    {
        foreach ($this->translations as $id => $translation) {
            $this->translations[$id] = clone $translation;
        }

        $this->headers = clone $this->headers;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    public function count(): int
    {
        return count($this->translations);
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function add(Translation $translation): self
    {
        $id = $translation->getId();

        $this->translations[$id] = $translation;

        return $this;
    }

    public function remove(Translation $translation): self
    {
        $key = array_search($translation, $this->translations);

        if ($key !== false) {
            unset($this->translations[$key]);
        }

        return $this;
    }

    public function setDomain(string $domain): self
    {
        $this->getHeaders()->setDomain($domain);

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->getHeaders()->getDomain();
    }

    public function setLanguage(string $language): self
    {
        $info = Language::getById($language);

        if (empty($info)) {
            throw new InvalidArgumentException(sprintf('The language "%s" is not valid', $language));
        }

        $this->getHeaders()
            ->setLanguage($language)
            ->setPluralForm(count($info->categories), $info->formula);

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->getHeaders()->getLanguage();
    }

    public function find(?string $context, string $original): ?Translation
    {
        foreach ($this->translations as $translation) {
            if ($translation->getContext() === $context && $translation->getOriginal() === $original) {
                return $translation;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return array_values($this->translations);
    }
}
