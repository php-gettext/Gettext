<?php

namespace Gettext;

use Gettext\Languages\Language;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * Class to manage a collection of translations.
 */
class Translations extends \ArrayObject
{
    const MERGE_ADD = 1;
    const MERGE_REMOVE = 2;
    const MERGE_HEADERS_MINES = 4;
    const MERGE_HEADERS_THEIRS = 8;
    const MERGE_LANGUAGE_OVERRIDE = 16;
    const MERGE_DOMAIN_OVERRIDE = 32;

    const HEADER_LANGUAGE = 'Language';
    const HEADER_PLURAL = 'Plural-Forms';
    const HEADER_DOMAIN = 'X-Domain';

    public static $insertDate = true;

    private $headers;

    /**
     * @see \ArrayObject::__construct()
     */
    public function __construct($input = [], $flags = 0, $iterator_class = 'ArrayIterator')
    {
        $this->headers = [
            'Project-Id-Version' => '',
            'Report-Msgid-Bugs-To' => '',
            'Last-Translator' => '',
            'Language-Team' => '',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
        ];

        if (static::$insertDate) {
            $this->headers['POT-Creation-Date'] = $this->headers['PO-Revision-Date'] = date('c');
        }

        $this->headers[self::HEADER_LANGUAGE] = '';
        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * Magic method to create new instances using extractors
     * For example: Translations::fromMoFile($filename, $options);.
     *
     * @return Translations
     */
    public static function __callStatic($name, $arguments)
    {
        if (!preg_match('/^from(\w+)(File|String)$/i', $name, $matches)) {
            throw new BadMethodCallException("The method $name does not exists");
        }

        return call_user_func_array([new static(), 'add'.ucfirst($name)], $arguments);
    }

    /**
     * Magic method to import/export the translations to a specific format
     * For example: $translations->toMoFile($filename, $options);
     * For example: $translations->addFromMoFile($filename, $options);.
     *
     * @return self|bool
     */
    public function __call($name, $arguments)
    {
        if (!preg_match('/^(addFrom|to)(\w+)(File|String)$/i', $name, $matches)) {
            throw new BadMethodCallException("The method $name does not exists");
        }

        if ($matches[1] === 'addFrom') {
            $extractor = 'Gettext\\Extractors\\'.$matches[2].'::from'.$matches[3];
            $source = array_shift($arguments);
            $options = array_shift($arguments) ?: [];

            call_user_func($extractor, $source, $this, $options);

            return $this;
        }

        $generator = 'Gettext\\Generators\\'.$matches[2].'::to'.$matches[3];

        array_unshift($arguments, $this);

        return call_user_func_array($generator, $arguments);
    }

    /**
     * Magic method to clone each translation on clone the translations object.
     */
    public function __clone()
    {
        $array = [];

        foreach ($this as $key => $translation) {
            $array[$key] = clone $translation;
        }

        $this->exchangeArray($array);
    }

    /**
     * Control the new translations added.
     *
     * @param mixed       $index
     * @param Translation $value
     *
     * @throws InvalidArgumentException If the value is not an instance of Gettext\Translation
     *
     * @return Translation
     */
    public function offsetSet($index, $value)
    {
        if (!($value instanceof Translation)) {
            throw new InvalidArgumentException('Only instances of Gettext\\Translation must be added to a Gettext\\Translations');
        }

        $id = $value->getId();

        if ($this->offsetExists($id)) {
            $this[$id]->mergeWith($value);

            return $this[$id];
        }

        parent::offsetSet($id, $value);

        return $value;
    }

    /**
     * Set the plural definition.
     *
     * @param int    $count
     * @param string $rule
     * 
     * @return self
     */
    public function setPluralForms($count, $rule)
    {
        $this->setHeader(self::HEADER_PLURAL, "nplurals={$count}; plural={$rule};");

        return $this;
    }

    /**
     * Returns the parsed plural definition.
     *
     * @param null|array [count, rule]
     */
    public function getPluralForms()
    {
        $header = $this->getHeader(self::HEADER_PLURAL);

        if (!empty($header) && preg_match('/^nplurals\s*=\s*(\d+)\s*;\s*plural\s*=\s*([^;]+)\s*;$/', $header, $matches)) {
            return [intval($matches[1]), $matches[2]];
        }
    }

    /**
     * Set a new header.
     *
     * @param string $name
     * @param string $value
     * 
     * @return self
     */
    public function setHeader($name, $value)
    {
        $name = trim($name);
        $this->headers[$name] = trim($value);

        return $this;
    }

    /**
     * Returns a header value.
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
     * Returns all header for this translations (in alphabetic order).
     *
     * @return array
     */
    public function getHeaders()
    {
        ksort($this->headers);

        return $this->headers;
    }

    /**
     * Removes all headers.
     * 
     * @return self
     */
    public function deleteHeaders()
    {
        $this->headers = [];

        return $this;
    }

    /**
     * Removes one header.
     *
     * @param string $name
     * 
     * @return self
     */
    public function deleteHeader($name)
    {
        unset($this->headers[$name]);

        return $this;
    }

    /**
     * Returns the language value.
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->getHeader(self::HEADER_LANGUAGE);
    }

    /**
     * Sets the language and the plural forms.
     *
     * @param string $language
     * 
     * @throws InvalidArgumentException if the language hasn't been recognized
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->setHeader(self::HEADER_LANGUAGE, trim($language));

        if (($info = Language::getById($language))) {
            return $this->setPluralForms(count($info->categories), $info->formula);
        }

        throw new InvalidArgumentException(sprintf('The language "%s" is not valid', $language));
    }

    /**
     * Checks whether the language is empty or not.
     *
     * @return bool
     */
    public function hasLanguage()
    {
        $language = $this->getLanguage();

        return (is_string($language) && ($language !== '')) ? true : false;
    }

    /**
     * Set a new domain for this translations.
     *
     * @param string $domain
     * 
     * @return self
     */
    public function setDomain($domain)
    {
        $this->setHeader(self::HEADER_DOMAIN, trim($domain));

        return $this;
    }

    /**
     * Returns the domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->getHeader(self::HEADER_DOMAIN);
    }

    /**
     * Checks whether the domain is empty or not.
     *
     * @return bool
     */
    public function hasDomain()
    {
        $domain = $this->getDomain();

        return (is_string($domain) && ($domain !== '')) ? true : false;
    }

    /**
     * Search for a specific translation.
     *
     * @param string|Translation $context  The context of the translation or a translation instance
     * @param string             $original The original string
     *
     * @return Translation|false
     */
    public function find($context, $original = '')
    {
        if ($context instanceof Translation) {
            $id = $context->getId();
        } else {
            $id = Translation::generateId($context, $original);
        }

        return $this->offsetExists($id) ? $this[$id] : false;
    }

    /**
     * Creates and insert/merges a new translation.
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
     * Merges this translations with other translations.
     *
     * @param Translations $translations        The translations instance to merge with
     * @param int          $options             
     * 
     * @return self
     */
    public function mergeWith(Translations $translations, $options = self::MERGE_ADD | Translation::MERGE_TRANSLATION_OVERRIDE)
    {
        $this->mergeHeaders($translations, $options);
        $this->mergeTranslations($translations, $options);

        return $this;
    }

    /**
     * Merge the headers of two translations
     * 
     * @param Translations $translations
     * @param int          $options
     */
    private function mergeHeaders(Translations $translations, $options)
    {
        if ($options & self::MERGE_HEADERS_THEIRS) {
            $this->deleteHeader();
        }

        if (!($options & self::MERGE_HEADERS_MINES)) {
            foreach ($translations->getHeaders() as $name => $value) {
                $current = $this->getHeader($name);

                if ($current === null) {
                    $this->setHeader($name, $value);
                    continue;
                }

                switch ($name) {
                    case self::HEADER_LANGUAGE:
                    case self::HEADER_PLURAL:
                        if (!$current || ($value && ($options & self::MERGE_LANGUAGE_OVERRIDE))) {
                            $this->setHeader($name, $value);
                        }
                        continue 2;

                    case self::HEADER_DOMAIN:
                        if (!$current || ($value && ($options & self::MERGE_DOMAIN_OVERRIDE))) {
                            $this->setHeader($name, $value);
                        }
                        continue 2;

                    default:
                        if (!$current) {
                            $this->setHeader($name, $value);
                        }
                }
            }
        }
    }

    /**
     * Merge the translations of two translations
     * 
     * @param Translations $translations
     * @param int          $options
     */
    private function mergeTranslations(Translations $translations, $options)
    {
        $add = (boolean) ($options & self::MERGE_ADD);

        foreach ($translations as $entry) {
            if (($existing = $this->find($entry))) {
                $existing->mergeWith($entry, $options);
            } elseif ($add) {
                $this[] = clone $entry;
            }
        }

        if ($options & self::MERGE_REMOVE) {
            $filtered = [];

            foreach ($this as $entry) {
                if ($translations->find($entry)) {
                    $filtered[] = $entry;
                }
            }

            $this->exchangeArray($filtered);
        }
    }
}
