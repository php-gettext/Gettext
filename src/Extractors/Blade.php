<?php

namespace Gettext\Extractors;

use Gettext\Translations;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Class to get gettext strings from blade.php files returning arrays.
 */
class Blade extends Extractor implements ExtractorInterface
{
    public static $cachePath;

    /**
     * {@inheritdoc}
     */
    public static function fromString($string, Translations $translations = null, $file = '')
    {
        $cachePath = empty(static::$cachePath) ? sys_get_temp_dir() : static::$cachePath;
        $bladeCompiler = new BladeCompiler(new Filesystem(), $cachePath);
        $string = $bladeCompiler->compileString($string);

        return PhpCode::fromString($string, $translations, $file);
    }
}
