<?php

namespace Hyvor\Phrosemirror\Converters\HtmlSerializer;

use Hyvor\Phrosemirror\Document\Node;

/**
 * @experimental
 * See NodeType.php
 */
class Context
{

    public function __construct(
        public Node $node,
        public Node $topNode,
        public string $children,
    ) {}

}