<?php
namespace Gettext\Utils;

class Locales
{
    /**
     * Language definitions
     * @var array
     * @link http://localization-guide.readthedocs.org/en/latest/l10n/pluralforms.html
     */
    protected static $localeInfo = array(
        'ach' => array('name' => 'Acholi', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'af' => array('name' => 'Afrikaans', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ak' => array('name' => 'Akan', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'am' => array('name' => 'Amharic', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'an' => array('name' => 'Aragonese', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'anp' => array('name' => 'Angika', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ar' => array('name' => 'Arabic', 'plurals' => 6, 'pluralRule' => '(n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5)'),
        'arn' => array('name' => 'Mapudungun', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'as' => array('name' => 'Assamese', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ast' => array('name' => 'Asturian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ay' => array('name' => 'Aymará', 'plurals' => 1, 'pluralRule' => '0'),
        'az' => array('name' => 'Azerbaijani', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'be' => array('name' => 'Belarusian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'bg' => array('name' => 'Bulgarian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'bn' => array('name' => 'Bengali', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'bo' => array('name' => 'Tibetan', 'plurals' => 1, 'pluralRule' => '0'),
        'br' => array('name' => 'Breton', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'brx' => array('name' => 'Bodo', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'bs' => array('name' => 'Bosnian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'ca' => array('name' => 'Catalan', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'cgg' => array('name' => 'Chiga', 'plurals' => 1, 'pluralRule' => '0'),
        'cs' => array('name' => 'Czech', 'plurals' => 3, 'pluralRule' => '(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2'),
        'csb' => array('name' => 'Kashubian', 'plurals' => 3, 'pluralRule' => '(n==1) ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2'),
        'cy' => array('name' => 'Welsh', 'plurals' => 4, 'pluralRule' => '(n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3'),
        'da' => array('name' => 'Danish', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'de' => array('name' => 'German', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'doi' => array('name' => 'Dogri', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'dz' => array('name' => 'Dzongkha', 'plurals' => 1, 'pluralRule' => '0'),
        'el' => array('name' => 'Greek', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'en' => array('name' => 'English', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'eo' => array('name' => 'Esperanto', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'es' => array('name' => 'Spanish', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'es_AR' => array('name' => 'Spanish (Argentina)', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'et' => array('name' => 'Estonian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'eu' => array('name' => 'Basque', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'fa' => array('name' => 'Persian', 'plurals' => 1, 'pluralRule' => '0'),
        'ff' => array('name' => 'Fulah', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'fi' => array('name' => 'Finnish', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'fil' => array('name' => 'Filipino', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'fo' => array('name' => 'Faroese', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'fr' => array('name' => 'French', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'fur' => array('name' => 'Friulian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'fy' => array('name' => 'Frisian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ga' => array('name' => 'Irish', 'plurals' => 5, 'pluralRule' => '(n==1) ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4'),
        'gd' => array('name' => 'Scottish Gaelic', 'plurals' => 4, 'pluralRule' => '(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3'),
        'gl' => array('name' => 'Galician', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'gu' => array('name' => 'Gujarati', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'gun' => array('name' => 'Gun', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'ha' => array('name' => 'Hausa', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'he' => array('name' => 'Hebrew', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'hi' => array('name' => 'Hindi', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'hne' => array('name' => 'Chhattisgarhi', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'hr' => array('name' => 'Croatian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'hu' => array('name' => 'Hungarian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'hy' => array('name' => 'Armenian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ia' => array('name' => 'Interlingua', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'id' => array('name' => 'Indonesian', 'plurals' => 1, 'pluralRule' => '0'),
        'is' => array('name' => 'Icelandic', 'plurals' => 2, 'pluralRule' => '(n%10!=1 || n%100==11)'),
        'it' => array('name' => 'Italian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ja' => array('name' => 'Japanese', 'plurals' => 1, 'pluralRule' => '0'),
        'jbo' => array('name' => 'Lojban', 'plurals' => 1, 'pluralRule' => '0'),
        'jv' => array('name' => 'Javanese', 'plurals' => 2, 'pluralRule' => '(n != 0)'),
        'ka' => array('name' => 'Georgian', 'plurals' => 1, 'pluralRule' => '0'),
        'kk' => array('name' => 'Kazakh', 'plurals' => 1, 'pluralRule' => '0'),
        'kl' => array('name' => 'Greenlandic', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'km' => array('name' => 'Khmer', 'plurals' => 1, 'pluralRule' => '0'),
        'kn' => array('name' => 'Kannada', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ko' => array('name' => 'Korean', 'plurals' => 1, 'pluralRule' => '0'),
        'ku' => array('name' => 'Kurdish', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'kw' => array('name' => 'Cornish', 'plurals' => 4, 'pluralRule' => '(n==1) ? 0 : (n==2) ? 1 : (n == 3) ? 2 : 3'),
        'ky' => array('name' => 'Kyrgyz', 'plurals' => 1, 'pluralRule' => '0'),
        'lb' => array('name' => 'Letzeburgesch', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ln' => array('name' => 'Lingala', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'lo' => array('name' => 'Lao', 'plurals' => 1, 'pluralRule' => '0'),
        'lt' => array('name' => 'Lithuanian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'lv' => array('name' => 'Latvian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2)'),
        'mai' => array('name' => 'Maithili', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'mfe' => array('name' => 'Mauritian Creole', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'mg' => array('name' => 'Malagasy', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'mi' => array('name' => 'Maori', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'mk' => array('name' => 'Macedonian', 'plurals' => 2, 'pluralRule' => 'n==1 || n%10==1 ? 0 : 1'),
        'ml' => array('name' => 'Malayalam', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'mn' => array('name' => 'Mongolian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'mni' => array('name' => 'Manipuri', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'mnk' => array('name' => 'Mandinka', 'plurals' => 3, 'pluralRule' => '(n==0 ? 0 : n==1 ? 1 : 2)'),
        'mr' => array('name' => 'Marathi', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ms' => array('name' => 'Malay', 'plurals' => 1, 'pluralRule' => '0'),
        'mt' => array('name' => 'Maltese', 'plurals' => 4, 'pluralRule' => '(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3)'),
        'my' => array('name' => 'Burmese', 'plurals' => 1, 'pluralRule' => '0'),
        'nah' => array('name' => 'Nahuatl', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'nap' => array('name' => 'Neapolitan', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'nb' => array('name' => 'Norwegian Bokmal', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ne' => array('name' => 'Nepali', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'nl' => array('name' => 'Dutch', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'nn' => array('name' => 'Norwegian Nynorsk', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'no' => array('name' => 'Norwegian (old code)', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'nso' => array('name' => 'Northern Sotho', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'oc' => array('name' => 'Occitan', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'or' => array('name' => 'Oriya', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'pa' => array('name' => 'Punjabi', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'pap' => array('name' => 'Papiamento', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'pl' => array('name' => 'Polish', 'plurals' => 3, 'pluralRule' => '(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'pms' => array('name' => 'Piemontese', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ps' => array('name' => 'Pashto', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'pt' => array('name' => 'Portuguese', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'pt_BR' => array('name' => 'Portuguese (Brazil)', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'rm' => array('name' => 'Romansh', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ro' => array('name' => 'Romanian', 'plurals' => 3, 'pluralRule' => '(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2)'),
        'ru' => array('name' => 'Russian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'rw' => array('name' => 'Kinyarwanda', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sah' => array('name' => 'Yakut', 'plurals' => 1, 'pluralRule' => '0'),
        'sat' => array('name' => 'Santali', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sco' => array('name' => 'Scots', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sd' => array('name' => 'Sindhi', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'se' => array('name' => 'Northern Sami', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'si' => array('name' => 'Sinhala', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sk' => array('name' => 'Slovak', 'plurals' => 3, 'pluralRule' => '(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2'),
        'sl' => array('name' => 'Slovenian', 'plurals' => 4, 'pluralRule' => '(n%100==1 ? 1 : n%100==2 ? 2 : n%100==3 || n%100==4 ? 3 : 0)'),
        'so' => array('name' => 'Somali', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'son' => array('name' => 'Songhay', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sq' => array('name' => 'Albanian', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sr' => array('name' => 'Serbian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'su' => array('name' => 'Sundanese', 'plurals' => 1, 'pluralRule' => '0'),
        'sv' => array('name' => 'Swedish', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'sw' => array('name' => 'Swahili', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'ta' => array('name' => 'Tamil', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'te' => array('name' => 'Telugu', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'tg' => array('name' => 'Tajik', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'th' => array('name' => 'Thai', 'plurals' => 1, 'pluralRule' => '0'),
        'ti' => array('name' => 'Tigrinya', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'tk' => array('name' => 'Turkmen', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'tr' => array('name' => 'Turkish', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'tt' => array('name' => 'Tatar', 'plurals' => 1, 'pluralRule' => '0'),
        'ug' => array('name' => 'Uyghur', 'plurals' => 1, 'pluralRule' => '0'),
        'uk' => array('name' => 'Ukrainian', 'plurals' => 3, 'pluralRule' => '(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
        'ur' => array('name' => 'Urdu', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'uz' => array('name' => 'Uzbek', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'vi' => array('name' => 'Vietnamese', 'plurals' => 1, 'pluralRule' => '0'),
        'wa' => array('name' => 'Walloon', 'plurals' => 2, 'pluralRule' => '(n > 1)'),
        'wo' => array('name' => 'Wolof', 'plurals' => 1, 'pluralRule' => '0'),
        'yo' => array('name' => 'Yoruba', 'plurals' => 2, 'pluralRule' => '(n != 1)'),
        'zh' => array('name' => 'Chinese', 'plurals' => 1, 'pluralRule' => '0'),
        // 'zh' => array('name' => '', 'plurals' => 2, 'pluralRule' => '(n > 1)'), // In rare cases where plural form introduces difference in personal pronoun (such as her vs. they, we vs. I), the plural form is different.
    );

    /**
     * Returns the info of a locale
     *
     * @param string $code
     *
     * @return null|array
     */
    public static function getLocaleInfo($code)
    {
        $result = null;

        // Locale identifier structure: see Unicode Language Identifier - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Unicode_language_identifier
        if (is_string($code) && preg_match('/^([a-z]{2,3})(?:[_\-]([a-z]{4}))?(?:[_\-]([a-z]{2}|[0-9]{3}))?(?:$|[_\-])/i', $code, $matches)) {
            $language = strtolower($matches[1]);
            $script = isset($matches[2]) ? ucfirst(strtolower($matches[2])) : '';
            $territory = isset($matches[3]) ? strtoupper($matches[3]) : '';

            // Structure precedence: see Likely Subtags - http://www.unicode.org/reports/tr35/tr35-31/tr35.html#Likely_Subtags
            $variants = array();

            if (strlen($script) && strlen($territory)) {
                $variants[] = "{$language}_{$script}_{$territory}";
            }

            if (strlen($script)) {
                $variants[] = "{$language}_{$script}";
            }

            if (strlen($territory)) {
                $variants[] = "{$language}_{$territory}";
            }

            $variants[] = $language;

            foreach ($variants as $variant) {
                if (isset(static::$localeInfo[$variant])) {
                    $result = static::$localeInfo[$variant];
                    break;
                }
            }
        }

        return $result;
    }
}
