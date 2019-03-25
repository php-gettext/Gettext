<?php

namespace Gettext\Utils;

use Peast\Peast;
use Peast\Syntax\Node\CallExpression;
use Peast\Syntax\Node\MemberExpression;
use Peast\Syntax\Node\Node;
use Peast\Traverser;

class JsFunctionsScanner extends FunctionsScanner
{
    private $ast;

    /**
     * Constructor.
     *
     * @param string $code The php code to scan
     */
    public function __construct($code)
    {
        $this->ast = Peast::latest($code, [
            'sourceType' => Peast::SOURCE_TYPE_MODULE,
            'jsx' => true,
        ])->parse();
    }

    public function getFunctions(array $constants = [])
    {
        $functions = [];

        $traverser = new Traverser;
        $traverser->addFunction(function ($node) use (&$functions) {
            if (!$this->isCallExpression($node)) {
                return;
            }

            $functions[] = [
                $this->getNodeName($node),
                $this->getStartingLineNumber($node),
                $this->getArguments($node),
            ];
        });

        $traverser->traverse($this->ast);

        return $functions;
    }

    /**
     * Checks if given $node is a CallExpression.
     *
     * @param Node $node
     *
     * @return bool
     */
    private function isCallExpression(Node $node) : bool
    {
        return $node instanceof CallExpression || $node->getType() === 'CallExpression';
    }

    /**
     * Returns given $node's name.
     *
     * @param Node $node
     *
     * @return string|null
     */
    private function getNodeName(Node $node)
    {
        if (method_exists($node, 'getCallee')) {
            return $this->getNodeName($node->getCallee());
        }

        if ($node instanceof MemberExpression && $node->getProperty() instanceof Node) {
            return $this->getNodeName($node->getProperty());
        }

        if (method_exists($node, 'getName')) {
            return $node->getName();
        }

        return null;
    }

    /**
     * Returns the starting line number of given $node.
     *
     * @param Node $node
     *
     * @return int
     */
    private function getStartingLineNumber(Node $node) : int
    {
        return $node->getLocation()->getStart()->getLine();
    }

    /**
     * Returns an array of arguments for given $node.
     *
     * @param Node $node
     *
     * @return array
     */
    private function getArguments(Node $node) :  array
    {
        return array_map(function($argument) {
            return $this->getArgumentValue($argument);
        }, $node->getArguments());
    }

    /**
     * Returns argument value of given $node.
     *
     * @param Node $node
     *
     * @return string|null
     */
    private function getArgumentValue(Node $node)
    {
        if ($node->getType() === 'Literal') {
            return $node->getValue();
        }

        return null;
    }
}
