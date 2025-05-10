<?php

namespace App\Doctrine\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;

abstract class BaseFunction extends FunctionNode
{
    public const T_COMMA             = 8;

    protected string $functionPrototype;

    /** @var string[] */
    protected array $nodesMapping = [];

    /** @var Node[] */
    protected array $nodes = [];

    abstract protected function customiseFunction(): void;

    protected function setFunctionPrototype(string $functionPrototype): void
    {
        $this->functionPrototype = $functionPrototype;
    }

    protected function addNodeMapping(string $parserMethod): void
    {
        $this->nodesMapping[] = $parserMethod;
    }

    public function parse(Parser $parser): void
    {
        $this->customiseFunction();

        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->feedParserWithNodes($parser);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    protected function feedParserWithNodes(Parser $parser): void
    {
        $nodesMappingCount = count($this->nodesMapping);
        $lastNode = $nodesMappingCount - 1;
        for ($i = 0; $i < $nodesMappingCount; $i++) {
            $parserMethod = $this->nodesMapping[$i];
            $this->nodes[$i] = $parser->{$parserMethod}();
            if ($i >= $lastNode) {
                continue;
            }

            $parser->match(TokenType::T_COMMA);
        }
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [];
        foreach ($this->nodes as $node) {
            $dispatched[] = $node->dispatch($sqlWalker);
        }

        return vsprintf($this->functionPrototype, $dispatched);
    }
}
