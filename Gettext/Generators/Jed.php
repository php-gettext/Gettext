<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Jed extends Generator
{
	/**
	 * Generates a string with the entries ready to save in a file
	 * 
	 * @param Entries $entries
	 * @param boolean $encoded True to encode to javascript, false to return an array
	 * 
	 * @return array|string
	 */
    public static function generate(Entries $entries, $encoded = true)
    {
        $translations = PhpArray::generate($entries);

        return $encoded ? json_encode($translations) : $translations;
    }

    /**
     * Generates a Jed string adapted for multiple domains. If you need to generate the jed string
     * for a single domain, use Jed::generate directly as this is just a wrapper around that
     *
     * @param Entries $entries 
     * @param [...] variable list of Entries to combine into a single Jed file
     * @param boolean $encoded True to encode to javascript, false to return array 
     *      (this must alwasy be the last param given!)
     **/
    public static function generateMultipleDomains(Entries $entries) {
        $argList = func_get_args();
        if(count($argList) < 2) {
            throw new Exception('Jed::generateMultipleDomains expects at least 2 arguments');
        }

        $lastArg = end($argList);

        // allow a bool as the last element in the array to keep the signature
        // of generate consistent. However, if it is a bool we need to accommodate for it
        if(is_bool($lastArg)) {
            $encoded = (bool)$lastArg;
            array_pop($argList);
        }
        else {
            $encoded = true;
        }

        $allTranslations = array();
        foreach($argList as $entries) {
            $translations = self::generate($entries, false);
            $allTranslations = array_merge_recursive($allTranslations, $translations);
        }
        return $encoded ? json_encode($allTranslations) : $allTranslations;
    }
}
