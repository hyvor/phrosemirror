<?php

namespace Hyvor\Prosemirror\Document;

use ArrayIterator;
use Hyvor\Prosemirror\Types\Schema;
use Hyvor\Prosemirror\Util\JsonHelper;
use IteratorAggregate;
use Traversable;

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

    /**
     * @param InputJsonType $json Content JSON array with nodes
     */
    public static function fromJson(Schema $schema, $json) : self
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