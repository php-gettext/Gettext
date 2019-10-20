<?php

namespace Gettext;

use Gettext\Languages\Language;
use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use IteratorAggregate;
use ArrayIterator;
use Countable;

/**
 * Class to manage a collection of translations under the same domain.
 */
class Translations implements JsonSerializable, Countable, IteratorAggregate
{
    protected $translations = [];
    protected $headers;

    public function __construct()
    {
        $this->headers = new Headers();
    }

    public function __clone()
    {
        foreach ($this->translations as $id => $translation) {
            $this->translations[$id] = clone $translation;
        }

        $this->headers = clone $this->headers;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function add(Translation $translation): self
    {
        $id = $translation->getId();

        $this->translations[$id] = $translation;

        return $value;
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
            ->setPluralForms(count($info->categories), $info->formula);

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->getHeaders()->getLanguage();
    }
}
