<?php
declare(strict_types = 1);

namespace Gettext;

use JsonSerializable;
use IteratorAggregate;
use ArrayIterator;
use Countable;
use InvalidArgumentException;

/**
 * Class to manage the headers of translations.
 */
class Headers implements JsonSerializable, Countable, IteratorAggregate
{
    const HEADER_LANGUAGE = 'Language';
    const HEADER_PLURAL = 'Plural-Forms';
    const HEADER_DOMAIN = 'X-Domain';

    protected $headers = [];
    
    public function set(string $name, string $value): self
    {
        $this->headers[$name] = trim($value);

        return $this;
    }
    
    public function get(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
    
    public function delete(string $name): self
    {
        unset($this->headers[$name]);
        
        return $this;
    }

    public function clear(): self
    {
        $this->headers = [];

        return $this;
    }

    public function sort(): self
    {
        ksort($this->headers);

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    public function count(): int
    {
        return count($this->headers);
    }

    public function setLanguage(string $language): self
    {
        return $this->set(self::HEADER_LANGUAGE, $language);
    }

    public function getLanguage(): ?string
    {
        return $this->get(self::HEADER_LANGUAGE);
    }

    public function setDomain(string $domain): self
    {
        return $this->set(self::HEADER_DOMAIN, $domain);
    }

    public function getDomain(): ?string
    {
        return $this->get(self::HEADER_DOMAIN);
    }

    public function setPluralForm(int $count, string $rule): self
    {
        if (preg_match('/[a-z]/i', str_replace('n', '', $rule))) {
            throw new InvalidArgumentException(sprintf('Invalid Plural form: "%s"', $rule));
        }

        return $this->set(self::HEADER_PLURAL, sprintf('nplurals=%d; plural=%s;', $count, $rule));
    }

    /**
     * Returns the parsed plural definition.
     *
     * @param null|array [count, rule]
     */
    public function getPluralForm(): ?array
    {
        $header = $this->get(self::HEADER_PLURAL);

        if (!empty($header) && preg_match('/^nplurals\s*=\s*(\d+)\s*;\s*plural\s*=\s*([^;]+)\s*;$/', $header, $matches)) {
            return [intval($matches[1]), $matches[2]];
        }

        return null;
    }

    public function toArray(): array
    {
        return $this->headers;
    }
}
