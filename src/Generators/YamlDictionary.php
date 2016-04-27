<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Symfony\Component\Yaml\Dumper;

class YamlDictionary extends Generator implements GeneratorInterface
{
    /**
     * {@parentDoc}.
     */
    public static function toString(Translations $translations)
    {
        $array = PhpArray::toArray($translations);

        //for a simple json translation dictionary, one domain is supported
        $values = current($array);

        // remove meta / header data
        if (array_key_exists('', $values)) {
            unset($values['']);
        }

        //map to a simple yml dictionary (no plurals)
        $yml = new Dumper();
        $output = $yml->dump(
                array_map(
                    function ($val) {
                        return isset($val[1]) ? $val[1] : null;
                    },
                    $values
                ),
            1
        );

        return $output;
    }
}
