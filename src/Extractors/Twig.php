<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Twig_Loader_String;
use Twig_Environment;
use Twig_Extensions_Extension_I18n;

/**
 * Class to get gettext strings from twig files returning arrays
 */
class Twig extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        $twigCompiler = new Twig_Loader_String();
        $twig = new Twig_Environment($twigCompiler);

        $twig->addExtension(new Twig_Extensions_Extension_I18n());

        // add default global php gettext functions
        PhpCode::$functions['gettext'] = '__';
        PhpCode::$functions['ngettext'] = '__';
        PhpCode::$functions['_'] = '__';

        $string = $twig->compileSource($string);

        return PhpCode::fromString($string, $translations, $file);
    }
}
