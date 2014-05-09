<?php
namespace Gettext\Extractors;

use Gettext\Entries;

/**
 * Class to get gettext strings from javascript files
 */
class JsCode extends Extractor
{
    // Javascript function names to search
    public static $functions = array(
        '__' => '__',
        'n__' => 'n__',
        'p__' => 'p__'
    );


    /**
     * Parses a javascript file and append the translations found in the Entries instance
     * 
     * @param string  $file
     * @param Entries $entries
     */
    public static function parse($file, Entries $entries)
    {
        $strings = $regs = array();

        $content = file_get_contents($file);
        $encoding = mb_detect_encoding($content, array('UTF-8', 'ISO-8859-1', 'WINDOWS-1252'), true);

        if ($encoding && (($encoding !== 'UTF-8') || mb_check_encoding($content, 'UTF-8') === false)) {
            $content = utf8_encode($content);
        }

        $content = htmlspecialchars($content, ENT_NOQUOTES);

        $content = preg_replace_callback('# ( / (?: (?>[^/\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\/ )+ (?<!\\\\)/ ) [a-z]* \b #ix', function ($match) use (&$regs) {
            $counter = count($regs);
            $regs[$counter] = $match[1];

            return "<<reg{$counter}>>";
        }, $content);

        $content = preg_replace_callback(array(
            '# " ( (?: (?>[^"\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\" )* ) (?<!\\\\)" #ix',
            "# ' ( (?: (?>[^'\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\' )* ) (?<!\\\\)' #ix"
        ), function ($match) use (&$regs, &$strings) {
            $counter = count($strings);

            $strings[$counter] = preg_replace_callback("#<<reg(\d+)>>#", function ($match) use ($regs) {
                return $regs[$match[1]];
            }, $match[0]);

            return "<<s{$counter}>>";
        }, $content);

        $content = preg_replace("#(//.*?)$#m", '', $content);
        $content = preg_replace('#/\*(.*?)\*/#is', '', $content);

        $content = preg_replace_callback("#<<s(\d+)>>#", function ($match) use ($strings) {
            return $strings[$match[1]];
        }, $content);

        $keywords = implode('|', array_keys(self::$functions));
        $strings = array();

        preg_match_all('# (?:('.$keywords.')) \(\\ *" ( (?: (?>[^"\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\" )* ) (?<!\\\\)"\\ *\) #ix', $content, $matches1, PREG_SET_ORDER);
        $matches1 = self::stripQuotes($matches1, '"');

        preg_match_all("# (?:($keywords)) \(\\ *' ( (?: (?>[^'\\\\]++) | \\\\\\\\ | (?<!\\\\)\\\\(?!\\\\) | \\\\' )* ) (?<!\\\\)'\\ *\) #ix", $content, $matches2, PREG_SET_ORDER);
        $matches2 = self::stripQuotes($matches2, "'");

        foreach (array_merge($matches1, $matches2) as $match) {
            if (!isset(self::$functions[$match[1]])) {
                continue;
            }

            switch (self::$functions[$match[1]]) {
                case '__':
                    $original = $match[2];
                    $translation = $entries->find('', $original) ?: $entries->insert('', $original);
                    break;

                case 'n__':
                    $original = $match[2];
                    $plural = $match[3];
                    $translation = $entries->find('', $original, $plural) ?: $entries->insert('', $original, $plural);
                    break;

                case 'p__':
                    $context = $match[2];
                    $original = $match[3];
                    $translation = $entries->find($context, $original) ?: $entries->insert($context, $original);
                    break;
            }
        }
    }


    /**
     * Removes the quotes of a string ("hello" => hello)
     * 
     * @param array|string $match The string to strip quotes
     * @param string       $quote The quote type (single or double)
     * 
     * @return string
     */
    private static function stripQuotes($match, $quote)
    {
        if (is_array($match)) {
            foreach ($match as &$value) {
                $value = self::stripQuotes($value, $quote);
            }

            return $match;
        }

        if ($quote === '"') {
            return str_replace('\\"', '"', $match);
        }

        return str_replace("\\'", "'", $match);
    }
}
