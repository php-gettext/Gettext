<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Twig_Loader_String;
use Twig_Environment;
use Twig_Extensions_Extension_I18n;

/**
 * Class to get gettext strings from twig files returning arrays.
 */
class Twig extends Extractor implements ExtractorInterface
{
    /**
     * Twig instance.
     *
     * @var Twig_Environment
     */
    private static $twig;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations, array $options = [])
    {
        $string = self::getTwig()->compileSource($string);

        PhpCode::fromString($string, $translations, $options);
    }

    /**
     * Returns a Twig instance.
     *
     * @return Twig_Environment
     */
    private static function getTwig()
    {
        //Initialise twig
        if (self::$twig === null) {
            self::$twig = new Twig_Environment(new Twig_Loader_String());
            self::$twig->addExtension(new Twig_Extensions_Extension_I18n());
        }

        return self::$twig;
    }
}
