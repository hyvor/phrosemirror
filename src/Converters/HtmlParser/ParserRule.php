<?php

namespace Hyvor\Phrosemirror\Converters\HtmlParser;

use DOMElement;
use DOMNode;
use Hyvor\Phrosemirror\Types\AttrsType;

class ParserRule
{

    public function __construct(
        public ?string $node = null,
        public ?string $mark = null,

        public ?string $tag = null,

        /**
         * not supported yet
         */
        public ?string $style = null,

        /**
         * Custom function to get attributes of for the node or mark
         *
         * null: no attributes
         * false: this node does not match with this parser rule, try other rules
         * AttrsType: Set the node's correct attribute type
         *
         * @var callable(DOMElement) : (null|false|AttrsType) | null
         */
        public $getAttrs = null,

        /**
         * Customize children of the node when parsing
         * For example, when <pre> is matched, you can use this get text content ignoring all tags inside it
         * See Pre Node for an example
         *
         * @var callable(DOMElement) : (DOMNode|DOMNode[]|false) | null
         */
        public $getChildren = null,

        /**
         * Whitespace::COLLAPSE - collapses whitespaces
         * Whitespace::NORMALIZE - whitespace is preserved but newlines are normalized to spaces
         * Whitespace::PRESERVE - whitespace is preserved
         */
        public Whitespace $whitespace = Whitespace::COLLAPSE,
    )
    {}

}