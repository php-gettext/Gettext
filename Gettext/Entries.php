<?php
namespace Gettext;

/**
 * Class to manage a collection of translations
 */
class Entries extends \ArrayObject
{
    private $domain = null;
    private $language = null;
    private $headers = array();


    /**
     * Set a new header. There are two special headers which will automatically set their
     * related value in the object.
     *
     *  X-domain: When found, automatically sets the domain for this object
     *  Language: When found, automatically sets the language for this object
     *
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value)
    {
        $name = trim($name);
        $value = trim($value);
        if(strcasecmp($name, 'x-domain') == 0) {
            $this->setDomain($value);
        }

        if(strcasecmp($name, 'language') == 0) {
            $this->setLanguage($value);
        }

        $this->headers[$name] = $value;
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
        $this->language = $language;
    }

    /**
     * Set a new domain for this entries
     * 
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
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
}
