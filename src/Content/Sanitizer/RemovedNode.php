<?php

namespace Hyvor\Phrosemirror\Content\Sanitizer;

use Hyvor\Phrosemirror\Document\Node;

class RemovedNode
{

    public function __construct(
        public Node $node,
        public int $parentIndex,
    )
    {}

}