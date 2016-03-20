<?php

namespace Gettext\Generators;

use Gettext\Translations;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class JsonDictionary extends Generator implements GeneratorInterface
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
            unset( $values[''] );
        }

        //map to a simple json dictionary (no plurals)
        return static::json_format(
            array_map(
                function ($val) {
                    return isset( $val[1] ) ? $val[1] : '';
                },
                $values
            )
        );
    }

    /*
     * Pretty print some JSON.
     *
     * As taken from http://php.net/manual/en/function.json-encode.php#80339
     */
    protected static function json_format($data)
    {
        $tab          = "  ";
        $new_json     = "";
        $indent_level = 0;
        $in_string    = false;

        $json = json_encode($data);
        $len  = strlen($json);

        for ($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            switch ($char) {
                case '{':
                case '[':
                    if ( ! $in_string) {
                        $new_json .= $char."\n".str_repeat($tab, $indent_level + 1);
                        $indent_level++;
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if ( ! $in_string) {
                        $indent_level--;
                        $new_json .= "\n".str_repeat($tab, $indent_level).$char;
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case ',':
                    if ( ! $in_string) {
                        $new_json .= ",\n".str_repeat($tab, $indent_level);
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case ':':
                    if ( ! $in_string) {
                        $new_json .= ": ";
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case '"':
                    if ($c > 0 && $json[$c - 1] != '\\') {
                        $in_string = ! $in_string;
                    }
                default:
                    $new_json .= $char;
                    break;
            }
        }

        return $new_json;
    }
}
