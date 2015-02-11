<?php
namespace Gettext\Utils;

class Locales
{
    protected static $data;
    protected static $territories;

    /**
     * Returns all languages data
     * 
     * @return array
     */
    public static function getLanguages()
    {
        if (!isset(self::$data)) {
            self::$data = include __DIR__.'/languages-data.php';
        }

        return self::$data;
    }

    /**
     * Returns all territories data
     * 
     * @return array
     */
    public static function getTerritories()
    {
        if (!isset(self::$territories)) {
            self::$territories = include __DIR__.'/territories-data.php';
        }

        return self::$territories;
    }

    /**
     * Returns the info of a locale
     *
     * @param string $code
     *
     * @return null|array Returns null if $code is not valid, an array with data otherwise
     */
    public static function getLocaleInfo($code)
    {
        // Locale identifier structure: see Unicode Language Identifier - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Unicode_language_identifier
        if (is_string($code) && preg_match('/^([a-z]{2,3})(?:[_\-]([a-z]{4}))?(?:[_\-]([a-z]{2}|[0-9]{3}))?(?:$|[_\-])/i', $code, $matches)) {
            $language = strtolower($matches[1]);
            $script = isset($matches[2]) ? ucfirst(strtolower($matches[2])) : '';
            $territory = isset($matches[3]) ? strtoupper($matches[3]) : '';

            $languages = self::getLanguages();
            $territories = self::getTerritories();

            $variants = array();

            if (($script !== '') && ($territory !== '')) {
                $variants[] = "{$language}_{$script}_{$territory}";
            }

            if ($script !== '') {
                $variants[] = "{$language}_{$script}";
            }

            if ($territory !== '') {
                $variants[] = "{$language}_{$territory}";
            }

            $variants[] = $language;

            foreach ($variants as $id) {
                if (isset($languages[$id])) {
                    $result = $languages[$id];
                    $result['id'] = $id;
                    $result['script'] = $script;
                    $result['territory'] = $territory;
                    $result['territoryName'] = isset($territories[$territory]) ? $territories[$territory] : '';

                    return $result;
                }
            }
        }
    }
}
