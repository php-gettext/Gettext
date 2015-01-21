<?php
namespace Gettext;

/**
 * Class to manage a collection of translations
 */
class Translations extends \ArrayObject
{
    const MERGE_ADD = 1;
    const MERGE_REMOVE = 2;
    const MERGE_HEADERS = 4;
    const MERGE_REFERENCES = 8;
    const MERGE_COMMENTS = 16;

    private $domain = null;
    private $language = null;
    private $headers = array();

    /**
     * Magic method to create new instances using extractors
     * For example: Translations::fromMoFile($filename);
     *
     * @return Translations
     */
    public static function __callStatic($name, $arguments)
    {
        if (!preg_match('/^from(\w+)(File|String)$/i', $name, $matches)) {
            throw new \Exception("The method $name does not exists");
        }

        return call_user_func_array('Gettext\\Extractors\\'.$matches[1].'::from'.$matches[2], $arguments);
    }

    /**
     * Magic method to export the translations to a specific format
     * For example: $translations->toMoFile($filename);
     *
     * @return bool|string
     */
    public function __call($name, $arguments)
    {
        if (!preg_match('/^to(\w+)(File|String)$/i', $name, $matches)) {
            throw new \Exception("The method $name does not exists");
        }

        array_unshift($arguments, $this);

        return call_user_func_array('Gettext\\Generators\\'.$matches[1].'::to'.$matches[2], $arguments);
    }

    /**
     * Magic method to clone each translation on clone the translations object
     */
    public function __clone()
    {
        foreach ($this as $key => $translation) {
            $this[$key] = clone $translation;
        }
    }

    /**
     * Control the new translations added
     *
     * @param mixed $index
     * @param mixed $value
     *
     * @throws InvalidArgumentException If the value is not an instance of Gettext\Translation
     *
     * @return Translation
     */
    public function offsetSet($index, $value)
    {
        if (!($value instanceof Translation)) {
            throw new \InvalidArgumentException('Only instances of Gettext\\Translation must be added to a Gettext\\Translations');
        }

        if (($exists = $this->find($value))) {
            $exists->mergeWith($value);

            return $exists;
        }

        parent::offsetSet($index, $value);

        return $value;
    }

    /**
     * Set the plural definition
     *
     * @param integer $count
     * @param string  $rule
     */
    public function setPluralForms($count, $rule)
    {
        $this->setHeader('Plural-Forms', "nplurals={$count}; plural={$rule};");

        foreach ($this as $t) {
            $t->setPluralCount($count);
        }
    }

    /**
     * Set a new header.
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $this->headers[trim($name)] = trim($value);
    }

    /**
     * Returns a header value
     *
     * @param string $name
     *
     * @return null|string
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }

    /**
     * Returns all header for this translations
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns the language value
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the language and the plural forms
     *
     * @param string $language
     *
     * @return boolean Returns true if the plural rules has been updated, false if $language hasn't been recognized
     */
    public function setLanguage($language)
    {
        $result = false;
        $this->language = trim($language);

        if ($info = Utils\Locales::getLocaleInfo($language)) {
            $this->setPluralForms($info['plurals'], $info['pluralRule']);
            $result = true;
        }

        return $result;
    }

    /**
     * Set a new domain for this translations
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = trim($domain);
    }

    /**
     * Returns the domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Checks whether the domain is empty or not
     *
     * @return boolean
     */
    public function hasDomain()
    {
        return (isset($this->domain) && $this->domain !== '') ? true : false;
    }

    /**
     * Search for a specific translation
     *
     * @param string|Translation $context  The context of the translation or a translation instance
     * @param string             $original The original string
     *
     * @return Translation|false
     */
    public function find($context, $original = '')
    {
        if ($context instanceof Translation) {
            $original = $context->getOriginal();
            $context = $context->getContext();
        } else {
            $original = (string) $original;
            $context = (string) $context;
        }

        foreach ($this as $t) {
            if ($t->is($context, $original)) {
                return $t;
            }
        }

        return false;
    }

    /**
     * Creates and insert/merges a new translation
     *
     * @param string $context  The translation context
     * @param string $original The translation original string
     * @param string $plural   The translation original plural string
     *
     * @return Translation The translation created
     */
    public function insert($context, $original, $plural = '')
    {
        return $this->offsetSet(null, new Translation($context, $original, $plural));
    }

    /**
     * Merges this translations with other translations
     *
     * @param Translations $translations The translations instance to merge with
     * @param integer|null $method       One or various Translations::MERGE_* constants to define how to merge the translations
     */
    public function mergeWith(Translations $translations, $method = null)
    {
        if ($method === null) {
            $method = self::MERGE_ADD | self::MERGE_HEADERS | self::MERGE_COMMENTS;
        }

        if (!$this->getLanguage()) {
            $this->setLanguage($translations->getLanguage());
        }

        if (!$this->getDomain()) {
            $this->setDomain($translations->getDomain());
        }

        if ($method & self::MERGE_HEADERS) {
            foreach ($translations->getHeaders() as $name => $value) {
                if (!$this->getHeader($name)) {
                    $this->setHeader($name, $value);
                }
            }
        }

        $add = (boolean) ($method & self::MERGE_ADD);

        foreach ($translations as $entry) {
            if (($existing = $this->find($entry))) {
                $existing->mergeWith($entry, $method);
            } elseif ($add) {
                $this[] = clone $entry;
            }
        }

        if ($method & self::MERGE_REMOVE) {
            $filtered = array();

            foreach ($this as $entry) {
                if ($translations->find($entry)) {
                    $filtered[] = $entry;
                }
            }

            $this->exchangeArray($filtered);
        }
    }
}
