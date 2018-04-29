<?php


namespace Gettext\Utils;


class VueJsFunctionScanner extends JsFunctionsScanner
{
    /**
     * Number of lines that the the script of the template starts
     * @var int
     */
    public $lineOffset = 0;

    /**
     * @inheritdoc
     */
    public function getFunctions(array $constants = [])
    {
        $functions = parent::getFunctions($constants);

        // Add line offset to the functions because vue templates contain template at the top and the script is below.
        // When we parse, we parse only the script part so we need to add the line number
        $functions = array_map(function ($v) {
            $v[1] += $this->lineOffset - 1;
            return $v;
        }, $functions);

        return $functions;
    }
}