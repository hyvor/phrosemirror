<?php

namespace Hyvor\Phrosemirror\Document;

use ArrayIterator;
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Util\JsonHelper;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Node>
 */
class Fragment implements IteratorAggregate
{

    public function __construct(
        /**
         * @var Node[]
         */
        private array $nodes
    )
    {}

    /**
     * @return Node[]
     */
    public function all() : array
    {
        return $this->nodes;
    }

    public function nth(int $n) : ?Node
    {
        return $this->nodes[$n - 1] ?? null;
    }

    public function first() : ?Node
    {
        return $this->nth(1);
    }

    public function last() : ?Node
    {
        return $this->nth(count($this->nodes));
    }

    public function count() : int
    {
        return count($this->nodes);
    }

    public static function fromJson(Schema $schema, mixed $json) : self
    {

        $json = JsonHelper::getJsonArray($json);

        $content = [];

        foreach ($json as $nodeJson) {
            $content[] = Node::fromJson($schema, $nodeJson);
        }

        return new self($content);

    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->nodes);
    }
}