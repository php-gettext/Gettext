<?php
namespace Gettext;

/**
 * Class to manage a collection of translations
 */
class Entries extends \ArrayObject
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
     * Magic method to clone each translation on clone the entries object
     */
    public function __clone()
    {
        foreach ($this as $key => $translation) {
            $this[$key] = clone $translation;
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
     * Returns all header for this entries
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
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Sets the language value
     */
    public function setLanguage($language) {
        $this->language = trim($language);
    }

    /**
     * Set a new domain for this entries
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
     * @param string             $plural   The original plural string
     * 
     * @return Translation|false
     */
    public function find($context, $original = '', $plural = '')
    {
        if ($context instanceof Translation) {
            $original = $context->getOriginal();
            $plural = $context->getPlural();
            $context = $context->getContext();
        } else {
            $context = (string) $context;
            $original = (string) $original;
            $plural = (string) $plural;
        }

        foreach ($this as $t) {
            if ($t->is($context, $original, $plural)) {
                return $t;
            }
        }

        return false;
    }


    /**
     * Creates and insert a new translation
     * 
     * @param string $context  The translation context
     * @param string $original The translation original string
     * @param string $plural   The translation original plural string
     * 
     * @return Translation The translation created
     */
    public function insert($context, $original, $plural = '')
    {
        return $this[] = new Translation($context, $original, $plural);
    }


    /**
     * Merges this entries with other entries
     * 
     * @param Entries      $entries The entries instance to merge with
     * @param integer|null $method  One or various Entries::MERGE_* constants to define how to merge the entries
     */
    public function mergeWith(Entries $entries, $method = null)
    {
        if ($method === null) {
            $method = self::MERGE_ADD | self::MERGE_HEADERS | self::MERGE_COMMENTS;
        }

        if (!$this->getLanguage()) {
            $this->setLanguage($entries->getLanguage());
        }

        if (!$this->getDomain()) {
            $this->setDomain($entries->getDomain());
        }

        if ($method & self::MERGE_HEADERS) {
            foreach ($entries->getHeaders() as $name => $value) {
                if (!$this->getHeader($name)) {
                    $this->setHeader($name, $value);
                }
            }
        }

        $add = (boolean) $method & self::MERGE_ADD;
        $references = (boolean) $method & self::MERGE_REFERENCES;
        $comments = (boolean) $method & self::MERGE_COMMENTS;

        foreach ($entries as $entry) {
            if (($existing = $this->find($entry))) {
                $existing->mergeWith($entry, $references, $comments);
            } else if ($add) {
                $this[] = clone $entry;
            }
        }

        if ($method & self::MERGE_REMOVE) {
            $iterator = $this->getIterator();
            
            foreach ($iterator as $k => $entry) {
                if (!($existing = $entries->find($entry))) {
                    $iterator->offsetUnset($k);
                }
            }
        }
    }
}
