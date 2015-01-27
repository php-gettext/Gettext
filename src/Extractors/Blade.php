<?php
namespace Gettext\Extractors;

use Gettext\Translations;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

/**
 * Class to get gettext strings from blade.php files returning arrays
 */
class Blade extends Extractor implements ExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    protected static function fromStringDo($string, Translations $translations, $file)
    {
        $bladeCompiler = new BladeCompiler(new Filesystem(), null);
        $string = $bladeCompiler->compileString($string);

        PhpCode::fromString($string, $translations, $file);
    }
}
