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
        self::addExtension('Twig_Extensions_Extension_I18n');

        $string = self::$twig->compileSource($string);

        // add default global php gettext functions
        PhpCode::$functions['gettext'] = '__';
        PhpCode::$functions['ngettext'] = '__';
        PhpCode::$functions['_'] = '__';

        return PhpCode::fromString($string, $translations, $file);
    }

    /**
     * Initialise Twig if it isn't already, and add a given Twig extension.
     * This must be called before calling fromString()
     *
     * @param mixed Already initialised extension to add
     */
    public static function addExtension($extension)
    {
        // Get an instance of the extension
        // Support for string, closure and an object
        /*
        if (is_string($twig_extension)) {
            $twig_extension = new $twig_extension($this->app, $twig);
        } elseif (is_callable($twig_extension)) {
            $twig_extension = $twig_extension($this->app, $twig);
        } elseif (!is_object($twig_extension)) {
            throw new InvalidArgumentException('Incorrect extension type');
        }
        */

        // initialise twig
        if (!isset(self::$twig)) {
            $twigCompiler = new Twig_Loader_String();

            self::$twig = new Twig_Environment($twigCompiler);
        }

        if (!self::checkHasExtensionByClassName($extension)) {
            self::$twig->addExtension(new $extension());
        }
    }

    /**
     * Checks if a given Twig extension is already registered or not
     *
     * @param  string   Name of Twig extension to check
     * @return boolean  Whether it has been registered already or not
     */
    protected static function checkHasExtensionByClassName($className)
    {
        foreach (self::$twig->getExtensions() as $extension) {
            if ($className == get_class($extension)) {
                return true;
            }
        }

        return false;
    }
}
}
