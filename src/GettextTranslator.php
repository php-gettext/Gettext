<?php
namespace Gettext;

class GettextTranslator extends BaseTranslator implements TranslatorInterface
{
    protected $domain;

    /**
     * Constructor. Detects the current language using the environment variables
     *
     * @param string $language
     */
    public function __construct($language = null)
    {
        if (!function_exists('gettext')) {
            throw new \RuntimeException('This class require the gettext extension for PHP');
        }

        //detects the language environment respecting the priority order
        //http://php.net/manual/en/function.gettext.php#114062
        if (empty($language)) {
            $language = getenv('LANGUAGE') ?: getenv('LC_ALL') ?: getenv('LC_MESSAGES') ?: getenv('LANG');
        }

        if (!empty($language)) {
            $this->setLanguage($language);
        }
    }

    /**
     * Define the current locale
     *
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        setlocale(LC_MESSAGES, $language);
        putenv('LANGUAGE='.$language);

        return $this;
    }

    /**
     * Loads a gettext domain
     *
     * @param string $domain
     * @param string $path
     * @param null   $default
     *
     * @return self
     */
    public function loadDomain($domain, $path = null, $default = null)
    {
        bindtextdomain($domain, $path);
        bind_textdomain_codeset($domain, 'UTF-8');

        //Set default if $defaul === true or there's no default domain
        if ($default || empty($this->domain)) {
            textdomain($domain);
            $this->domain = $domain;
        }

        return $this;
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function gettext($original)
    {
        return gettext($original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function ngettext($original, $plural, $value)
    {
        return ngettext($original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dngettext($domain, $original, $plural, $value)
    {
        return dngettext($domain, $original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function npgettext($context, $original, $plural, $value)
    {
        return $this->ngettext($context."\x04".$original, $plural, $value);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function pgettext($context, $original)
    {
        return $this->gettext($context."\x04".$original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dgettext($domain, $original)
    {
        return dgettext($domain, $original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dpgettext($domain, $context, $original)
    {
        return $this->dgettext($domain, $context."\x04".$original);
    }

    /**
     * @see TranslatorInterface
     *
     * {@inheritdoc}
     */
    public function dnpgettext($domain, $context, $original, $plural, $value)
    {
        return $this->dngettext($domain, $context."\x04".$original, $plural, $value);
    }
}
