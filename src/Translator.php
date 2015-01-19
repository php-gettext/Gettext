<?php
namespace Gettext;

use Gettext\Generators\PhpArray;

class Translator
{
    public static $current;

    private $domain;
    private $dictionary = array();
    private $context_glue = "\004";
    private $plurals = array();

    /**
     * Set a translation instance as global, to use it with the gettext functions
     *
     * @param Translator $translator
     */
    public static function initGettextFunctions(Translator $translator)
    {
        self::$current = $translator;

        include_once __DIR__.'/translator_functions.php';
    }

    /**
     * Loads translation from a Translations instance, a file on an array
     *
     * @param Translations|string|array $translations
     *
     * @return Translator
     */
    public function loadTranslations($translations)
    {
        if (is_object($translations) && $translations instanceof Translations) {
            $translations = PhpArray::toArray($translations);
        } elseif (is_string($translations) && is_file($translations)) {
            $translations = include $translations;
        } elseif (!is_array($translations)) {
            throw new \InvalidArgumentException('Invalid Translator: only arrays, files or instance of Translations are allowed');
        }

        foreach ($translations as $translation) {
            $this->addTranslations($translation);
        }

        return $this;
    }

    /**
     * Set new translations to the dictionary
     *
     * @param array $translations
     */
    public function addTranslations(array $translations)
    {
        $info = isset($translations['']) ? $translations[''] : null;
        unset($translations['']);

        $domain = isset($info['domain']) ? $info['domain'] : 'messages';

        //Set the first domain loaded as default domain
        if (!$this->domain) {
            $this->domain = $domain;
        }

        if (!isset($this->dictionary[$domain])) {
            // If a plural form is set we extract those values
            $pluralForms = empty($info['plural-forms']) ? 'nplurals=2; plural=(n != 1)' : $info['plural-forms'];

            list($count, $code) = explode(';', $pluralForms, 2);

            // extract just the expression turn 'n' into a php variable '$n'.
            // Slap on a return keyword and semicolon at the end.
            $this->plurals[$domain] = array(
                'count' => (int) str_replace('nplurals=', '', $count),
                'code' => str_replace('plural=', 'return ', str_replace('n', '$n', $code)).';',
            );

            $this->dictionary[$domain] = $translations;
        } else {
            $this->dictionary[$domain] = array_replace_recursive($this->dictionary[$domain], $translations);
        }
    }

    /**
     * Clear all translations
     */
    public function clearTranslations()
    {
        $this->dictionary = array();
    }

    /**
     * Search and returns a translation
     *
     * @param string $domain
     * @param string $context
     * @param string $original
     *
     * @return array
     */
    public function getTranslation($domain, $context, $original)
    {
        $key = isset($context) ? $context.$this->context_glue.$original : $original;

        return isset($this->dictionary[$domain][$key]) ? $this->dictionary[$domain][$key] : false;
    }

    /**
     * Gets a translation using the original string
     *
     * @param string $original
     *
     * @return string
     */
    public function gettext($original)
    {
        return $this->dpgettext($this->domain, null, $original);
    }

    /**
     * Gets a translation checking the plural form
     *
     * @param string $original
     * @param string $plural
     * @param string $value
     *
     * @return string
     */
    public function ngettext($original, $plural, $value)
    {
        return $this->dnpgettext($this->domain, null, $original, $plural, $value);
    }

    /**
     * Gets a translation checking the domain and the plural form
     *
     * @param string $domain
     * @param string $original
     * @param string $plural
     * @param string $value
     *
     * @return string
     */
    public function dngettext($domain, $original, $plural, $value)
    {
        return $this->dnpgettext($domain, null, $original, $plural, $value);
    }

    /**
     * Gets a translation checking the context and the plural form
     *
     * @param string $context
     * @param string $original
     * @param string $plural
     * @param string $value
     *
     * @return string
     */
    public function npgettext($context, $original, $plural, $value)
    {
        return $this->dnpgettext($this->domain, $context, $original, $plural, $value);
    }

    /**
     * Gets a translation checking the context
     *
     * @param string $context
     * @param string $original
     *
     * @return string
     */
    public function pgettext($context, $original)
    {
        return $this->dpgettext($this->domain, $context, $original);
    }

    /**
     * Gets a translation checking the domain
     *
     * @param string $domain
     * @param string $original
     *
     * @return string
     */
    public function dgettext($domain, $original)
    {
        return $this->dpgettext($domain, null, $original);
    }

    /**
     * Gets a translation checking the domain and context
     *
     * @param string $domain
     * @param string $context
     * @param string $original
     *
     * @return string
     */
    public function dpgettext($domain, $context, $original)
    {
        $translation = $this->getTranslation($domain, $context, $original);

        if (isset($translation[1]) && $translation[1] !== '') {
            return $translation[1];
        }

        return $original;
    }

    /**
     * Gets a translation checking the domain, the context and the plural form
     *
     * @param string $domain
     * @param string $context
     * @param string $original
     * @param string $plural
     * @param string $value
     */
    public function dnpgettext($domain, $context, $original, $plural, $value)
    {
        $key = $this->isPlural($domain, $value);
        $translation = $this->getTranslation($domain, $context, $original);

        if (isset($translation[$key]) && $translation[$key] !== '') {
            return $translation[$key];
        }

        return ($key === 1) ? $original : $plural;
    }

    /**
     * Executes the plural decision code given the number to decide which
     * plural version to take.
     *
     * @param  string $domain
     * @param  string $n
     * @return int
     */
    public function isPlural($domain, $n)
    {
        //Not loaded domain, use a fallback
        if (!isset($this->plurals[$domain])) {
            return $n == 1 ? 1 : 2;
        }

        if (!isset($this->plurals[$domain]['function'])) {
            $this->plurals[$domain]['function'] = create_function('$n', self::fixTerseIfs($this->plurals[$domain]['code']));
        }

        if ($this->plurals[$domain]['count'] <= 2) {
            return (call_user_func($this->plurals[$domain]['function'], $n)) ? 2 : 1;
        }

        // We need to +1 because while (GNU) gettext codes assume 0 based,
        // this gettext actually stores 1 based.
        return (call_user_func($this->plurals[$domain]['function'], $n)) + 1;
    }

    /**
     * This function will recursively wrap failure states in brackets if they contain a nested terse if
     *
     * This because PHP can not handle nested terse if's unless they are wrapped in brackets.
     *
     * This code probably only works for the gettext plural decision codes.
     *
     * return ($n==1 ? 0 : $n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2);
     * becomes
     * return ($n==1 ? 0 : ($n%10>=2 && $n%10<=4 && ($n%100<10 || $n%100>=20) ? 1 : 2));
     *
     * @param  string $code  the terse if string
     * @param  bool   $inner If inner is true we wrap it in brackets
     * @return string A formatted terse If that PHP can work with.
     */
    private static function fixTerseIfs($code, $inner = false)
    {
        /**
         * (?P<expression>[^?]+)   Capture everything up to ? as 'expression'
         * \?                      ?
         * (?P<success>[^:]+)      Capture everything up to : as 'success'
         * :                       :
         * (?P<failure>[^;]+)      Capture everything up to ; as 'failure'
         */
        preg_match('/(?P<expression>[^?]+)\?(?P<success>[^:]+):(?P<failure>[^;]+)/', $code, $matches);

        // If no match was found then no terse if was present
        if (!isset($matches[0])) {
            return $code;
        }

        $expression = $matches['expression'];
        $success    = $matches['success'];
        $failure    = $matches['failure'];

        // Go look for another terse if in the failure state.
        $failure = self::fixTerseIfs($failure, true);
        $code = $expression.' ? '.$success.' : '.$failure;

        if ($inner) {
            return "($code)";
        }

        // note the semicolon. We need that for executing the code.
        return "$code;";
    }
}
