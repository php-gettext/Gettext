<?php
namespace Gettext\Generators;

use Gettext\Translations;

class Po extends Generator implements GeneratorInterface
{
    /**
     * {@parentDoc}
     */
    public static function toString(Translations $translations)
    {
        $lines = array('msgid ""', 'msgstr ""');

        $headers = array_replace(array(
            'Project-Id-Version' => '',
            'Report-Msgid-Bugs-To' => '',
            'Last-Translator' => '',
            'Language-Team' => '',
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Transfer-Encoding' => '8bit',
        ), $translations->getHeaders());

        $headers['POT-Creation-Date'] = $headers['PO-Revision-Date'] = date('c');

        foreach ($headers as $name => $value) {
            $lines[] = '"'.$name.': '.$value.'\\n"';
        }

        $lines[] = '';

        //Translations
        foreach ($translations as $translation) {
            if ($translation->hasComments()) {
                foreach ($translation->getComments() as $comment) {
                    $lines[] = '# '.$comment;
                }
            }

            if ($translation->hasReferences()) {
                foreach ($translation->getReferences() as $reference) {
                    $lines[] = '#: '.$reference[0].':'.$reference[1];
                }
            }

            if ($translation->hasContext()) {
                $lines[] = 'msgctxt '.self::quote($translation->getContext());
            }

            $msgid = self::multilineQuote($translation->getOriginal());

            if (count($msgid) === 1) {
                $lines[] = 'msgid '.$msgid[0];
            } else {
                $lines[] = 'msgid ""';
                $lines = array_merge($lines, $msgid);
            }

            if ($translation->hasPlural()) {
                $lines[] = 'msgid_plural '.self::quote($translation->getPlural());
                $lines[] = 'msgstr[0] '.self::quote($translation->getTranslation());

                foreach ($translation->getPluralTranslation() as $k => $v) {
                    $lines[] = 'msgstr['.($k + 1).'] '.self::quote($v);
                }
            } else {
                $lines[] = 'msgstr '.self::quote($translation->getTranslation());
            }

            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /**
     * Escapes and adds double quotes to a string
     *
     * @param string $string
     *
     * @return string
     */
    private static function quote($string)
    {
        return '"'.str_replace(array("\r", "\n", '"'), array('', '\n', '\\"'), $string).'"';
    }

    /**
     * Escapes and adds double quotes to a string
     *
     * @param string $string
     *
     * @return string
     */
    private static function multilineQuote($string)
    {
        $lines = explode("\n", $string);
        $last = count($lines) - 1;

        foreach ($lines as $k => $line) {
            if ($k === $last) {
                $lines[$k] = self::quote($line);
            } else {
                $lines[$k] = self::quote($line."\n");
            }
        }

        return $lines;
    }
}
