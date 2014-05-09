<?php
namespace Gettext\Extractors;

use Gettext\Entries;

/**
 * Class to get gettext strings from php files returning arrays
 */
class PhpArray extends Extractor
{
    /**
     * Includes a .php file with an array of translations and append to the Entries instance
     * 
     * @param string  $file
     * @param Entries $entries
     */
    public static function parse($file, Entries $entries)
    {
        $content = include($file);
        $content = $content['messages'];

        $entries_info = isset($content['']) ? $content[''] : null;
        unset($content['']);

        if (isset($entries_info['domain'])) {
            $entries->setDomain($entries_info['domain']);
        }

        $context_glue = '\u0004';

        foreach ($content as $key => $message) {
            $key = explode($context_glue, $key);

            $context = isset($key[1]) ? array_shift($key) : '';
            $original = array_shift($key);
            $plural = array_shift($message);
            $translation = array_shift($message);
            $plural_translation = array_shift($message);

            $entry = $entries->find($context, $original, $plural) ?: $entries->insert($context, $original, $plural);
            $entry->setTranslation($translation);
            $entry->setPluralTranslation($plural_translation);
        }
    }
}
