<?php

namespace Hyvor\Phrosemirror\Content;

use Hyvor\Phrosemirror\Content\Expr\ChoiceExpr;
use Hyvor\Phrosemirror\Content\Expr\Expr;
use Hyvor\Phrosemirror\Content\Expr\NodeTypeExpr;
use Hyvor\Phrosemirror\Content\Expr\OptionalExpr;
use Hyvor\Phrosemirror\Content\Expr\PlusExpr;
use Hyvor\Phrosemirror\Content\Expr\RangeExpr;
use Hyvor\Phrosemirror\Content\Expr\SequenceExpr;
use Hyvor\Phrosemirror\Content\Expr\StarExpr;
use Hyvor\Phrosemirror\Exception\ContentExpressionException;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;

/**
 * This is inspired by regular parsing the original Prosemirror library
 * https://github.com/ProseMirror/prosemirror-model/blob/master/src/content.ts#L190
 */
class ContentExpression
{

    /**
     * null means no content is allowed in the node.
     */
    public ?Expr $expr = null;

    public static function getExpr(
        ?string $string,
        Schema $schema,
    ) : ?Expr
    {
        $expr = new self($string, $schema);
        return $expr->expr;
    }

    public function __construct(
        private ?string $string,
        private Schema $schema,
    )
    {
        $this->parse();
    }

    private function parse() : void
    {
        if (!$this->string)
            return;

        $stream = new TokenStream($this->string);
        $this->expr = $this->parseExpr($stream);
    }

    private function parseExpr(TokenStream $stream) : Expr
    {

        /** @var Expr[] $exprs */
        $exprs = [];

        do {
            $exprs[] = $this->parseExprSeq($stream);
        } while ($stream->eat('|'));

        return count($exprs) === 1 ? $exprs[0] : new ChoiceExpr($exprs);

    }

    private function parseExprSeq(TokenStream $stream) : Expr
    {

        /** @var Expr[] $exprs */
        $exprs = [];

        do {
            $exprs[] = $this->parseExprSubscript($stream);
        } while ($stream->next() && $stream->next() !== ')' && $stream->next() !== '|');

        return count($exprs) === 1 ? $exprs[0] : new SequenceExpr($exprs);

    }

    private function parseExprSubscript(TokenStream $stream) : Expr
    {

        $expr = $this->parseExprAtom($stream);

        while (true) {
            if ($stream->eat('+')) {
                $expr = new PlusExpr($expr);
            } else if ($stream->eat('*')) {
                $expr = new StarExpr($expr);
            } else if ($stream->eat('?')) {
                $expr = new OptionalExpr($expr);
            } else if ($stream->eat('{')) {
                $expr = $this->parseExprRange($stream, $expr);
            } else {
                break;
            }
        }

        return $expr;

    }

    private function parseExprAtom(TokenStream $stream) : Expr
    {

        if ($stream->eat('(')) {
            $expr = $this->parseExpr($stream);
            if (!$stream->eat(')'))
                throw new ContentExpressionException('Missing closing parenthesis');
            return $expr;
        } else if (!preg_match('/\W/', strval($stream->next()))) {
            $exprs = array_map(
                fn (NodeType $name) => new NodeTypeExpr($name),
                $this->resolveNameToNodeTypes($stream, strval($stream->next()))
            );
            $stream->pos++;
            return count($exprs) === 1 ? $exprs[0] : new ChoiceExpr($exprs);
        } else {
            throw new ContentExpressionException('Unexpected token: ' . $stream->next());
        }

    }

    /**
     * @param TokenStream $stream
     * @param string $name
     * @return NodeType[]
     */
    private function resolveNameToNodeTypes(TokenStream $stream, string $name) : array
    {

        $types = $this->schema->nodes;

        $type = $this->schema->getNodeTypeByName($name);
        if ($type) return [$type];

        $result = [];
        foreach ($types as $nodeType) {
            if (in_array($name, $nodeType->getGroups())) {
                $result[] = $nodeType;
            }
        }

        if (count($result) === 0)
            throw new ContentExpressionException("No node type or group '$name' found");

        return $result;
    }

    private function parseExprRange(TokenStream $stream, Expr $expr) : Expr
    {

        $min = $this->parseNumber($stream);
        $max = $min;

        if ($stream->eat(',')) {
            if ($stream->next() !== '}') {
                $max = $this->parseNumber($stream);
            } else {
                $max = -1;
            }
        }

        if (!$stream->eat('}'))
            throw new ContentExpressionException("Unclosed braced range expression");

        return new RangeExpr($expr, $min, $max);

    }

    private function parseNumber(TokenStream $stream) : int
    {
        $next = $stream->next();

        if (!ctype_digit($next))
            throw new ContentExpressionException("Expected number, got '$next'");

        $stream->pos++;
        return (int) $next;
    }

}