<?php
namespace Gettext\Translators;

class GettextTranslator extends BaseTranslator implements TranslatorInterface
{
    private $domain;

    /**
     * Loads a gettext domain
     *
     * @param string $domain
     * @param string $path
     * @param bool   $default
     *
     * @return self
     */
    public function loadDomain($domain, $path, $default = false)
    {
        bindtextdomain($domain, $path);
        bind_textdomain_codeset($domain, 'UTF-8');

        if ($default) {
            textdomain($domain);
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
