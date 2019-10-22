<?php
declare(strict_types = 1);

namespace Gettext\Loader;

use Gettext\Translations;

interface LoaderInterface
{
    public function setTranslations(Translations $translations): void;

    public function getTranslations(): Translations;

    public function loadFile(string $filename): void;

    public function loadString(string $string): void;
}
