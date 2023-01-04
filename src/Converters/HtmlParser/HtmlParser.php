<?php

namespace Hyvor\Phrosemirror\Converters\HtmlParser;

use DOMDocument;
use DOMElement;
use DOMNode;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Mark;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Document\TextNode;
use Hyvor\Phrosemirror\Exception\ParserException;
use Hyvor\Phrosemirror\Types\AttrsType;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;

class HtmlParser
{

    private Document $document;

    /** @var Mark[] */
    private array $storedMarks = [];

    private Whitespace $storedWhitespace = Whitespace::COLLAPSE;

    /**
     * @param Schema $schema
     * @param ParserRule[]  $rules
     */
    public function __construct(
        private Schema $schema,
        private array $rules
    )
    {

        $docType = $this->schema->getNodeTypeByName('doc');

        if (!$docType)
            throw new ParserException('Schema must have a "doc" node type');

        $this->document = new Document($docType);

    }

    public function parse(string $html) : Document
    {

        // DOMDocument throws an error for custom HTML tags
        // this prevents it
        libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML($this->validXmlDoc($html));

        $body = $dom->getElementsByTagName('body')->item(0);

        if (!$body)
            throw new ParserException('Invalid HTML: no body element');

        $content = $this->parseElementChildren($body);

        $this->document->content = $content;

        return $this->document;

    }

    private function parseElementChildren(DOMNode $element) : Fragment
    {

        /** @var Node[] $nodes */
        $nodes = [];

        /** @var DOMNode $child */
        foreach ($element->childNodes as $child) {

            /**
             * First, check if the element is a node
             */
            if ($nodeMatch = $this->matchNode($child)) {


                $node = $nodeMatch['node'];
                $rule = $nodeMatch['rule'];

                $this->storedWhitespace = $rule->whitespace;

                // parse children
                if ($child->hasChildNodes() && $child instanceof DOMElement) {

                    if ($rule->getChildren) {
                        $this->replaceChildren($child, ($rule->getChildren)($child));
                    }

                    $node->content = $this->parseElementChildren($child);
                }

                // add marks to text nodes
                if ($node instanceof TextNode) {
                    $node->marks = $this->storedMarks;
                }

                $nodes[] = $node;

            } else if ($mark = $this->matchMark($child)) {

                // store marks and apply to children

                $this->storedMarks[] = $mark;

                if ($child->hasChildNodes()) {
                    $nodes = array_merge($nodes, $this->parseElementChildren($child)->all());
                }

                // remove stored mark
                array_pop($this->storedMarks);

            } else if ($child->hasChildNodes()) {
                // if the element is not a known node or mark, just parse children
                $nodes = array_merge($nodes, $this->parseElementChildren($child)->all());
            }

        }

        return new Fragment($nodes);

    }

    /**
     * @return null | array{node: Node, rule: ParserRule}
     */
    private function matchNode(DOMNode $domNode) : ?array
    {
        foreach ($this->rules as $rule) {

            if (!$rule->node)
                continue;

            if ($rule->tag !== null && $rule->tag === $domNode->nodeName) {

                $nodeType = $this->schema->getNodeTypeByName($rule->node);

                if (!$nodeType)
                    throw new ParserException('Invalid node type: ' . $rule->node);

                $nodeAttrs = new AttrsType;

                if ($rule->getAttrs && $domNode instanceof DOMElement) {
                    $attrs = ($rule->getAttrs)($domNode);

                    // if getAttrs returns false, this node does not match with this rule
                    if ($attrs === false)
                        continue;

                    if ($attrs)
                        $nodeAttrs = $attrs;
                }

                return [
                    'node' => $nodeType->isText() ?
                        $this->createTextNode($nodeType, $domNode->nodeValue ?? '') :
                        new Node($nodeType, $nodeAttrs),
                    'rule' => $rule
                ];

            }

        }
        return null;
    }

    private function createTextNode(NodeType $nodeType, string $text) : TextNode
    {

        if ($this->storedWhitespace === Whitespace::COLLAPSE) {
            // collapse whitespace
            $text = preg_replace('/\s+/', ' ', $text);
        } else if ($this->storedWhitespace === Whitespace::NORMALIZE) {
            // convert only new lines to space
            $text = preg_replace('/\n+/', " ", $text);
        }

        /** @var string $text */
        return TextNode::fromTypeAndText($nodeType, $text);

    }

    private function matchMark(DOMNode $domNode) : ?Mark
    {

        foreach ($this->rules as $rule) {

            if (!$rule->mark)
                continue;

            if ($rule->tag !== null && $rule->tag === $domNode->nodeName) {

                $markType = $this->schema->getMarkTypeByName($rule->mark);

                if (!$markType)
                    throw new ParserException('Invalid mark type: ' . $rule->mark);

                $markAttrs = new AttrsType;

                if ($rule->getAttrs && $domNode instanceof DOMElement) {
                    $attrs = ($rule->getAttrs)($domNode);

                    // if getAttrs returns false, this node does not match with this rule
                    if ($attrs === false)
                        continue;

                    if ($attrs)
                        $markAttrs = $attrs;
                }

                return new Mark($markType, $markAttrs);

            }

        }

        return null;

    }

    /**
     * @param DOMNode $element
     * @param DOMNode|DOMNode[] $children
     * @return void
     */
    private function replaceChildren(DOMNode $element, $children) : void
    {

        if (!is_array($children)) {
            $children = [$children];
        }

        foreach ($element->childNodes as $child) {
            $element->removeChild($child);
        }

        foreach ($children as $child) {
            $element->appendChild($child);
        }

    }


    private function validXmlDoc(string $html) : string
    {
        return '<?xml encoding="utf-8" ?>' . $html;
    }


    public static function fromSchema(Schema $schema) : self
    {

        /** @var ParserRule[] $rules */
        $rules = [];

        $all = array_merge($schema->nodes, $schema->marks);

        foreach ($all as $type) {
            $rulesOfThisType = $type->fromHtml();

            // add node/mark name to rule
            foreach ($rulesOfThisType as $rule) {
                if ($type instanceof NodeType) {
                    $rule->node = $type->name;
                } else {
                    $rule->mark = $type->name;
                }
            }
            $rules = array_merge($rules, $rulesOfThisType);

        }

        return new self($schema, $rules);

    }


}