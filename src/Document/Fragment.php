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
        private array $nodes = []
    )
    {}

    // === READ ===

    /**
     * @return Node[]
     */
    public function all() : array
    {
        return $this->nodes;
    }

    public function nth(int $n) : ?Node
    {
        return $this->nodes[$n] ?? null;
    }

    public function first() : ?Node
    {
        return $this->nth(0);
    }

    public function last() : ?Node
    {
        return $this->nth(count($this->nodes) - 1);
    }

    public function count() : int
    {
        return count($this->nodes);
    }

    /**
     * @param callable(Node) : mixed $callback
     * @return $this
     */
    public function each($callback) : self
    {
        foreach ($this->nodes as $node) {
            $callback($node);
        }
        return $this;
    }


    // === WRITE ===

    /**
     * Add a node to the start of the fragment
     */
    public function addNodeToStart(Node $node) : self
    {
        array_unshift($this->nodes, $node);
        return $this;
    }

    /**
     * Add a node to the end of the fragment
     */
    public function addNodeToEnd(Node $node) : self
    {
        $this->nodes[] = $node;
        return $this;
    }

    /**
     * Add a node to the end of the fragment
     */
    public function addNode(Node $node) : self
    {
        return $this->addNodeToEnd($node);
    }

    public function removeNode(Node $node) : self
    {
        return $this->setNodes(
            array_values( // reindex
                array_filter($this->nodes, fn($n) => $n !== $node)
            )
        );
    }

    public function replaceNode(Node $oldNode, Node $newNode) : self
    {
        return $this->setNodes(
            array_map(
                fn($n) => $n === $oldNode ? $newNode : $n,
                $this->nodes
            )
        );
    }

    public function getIndexOfNode(Node $node) : ?int
    {
        $search = array_search($node, $this->nodes, true);
        return $search === false ? null : intval($search);
    }

    /**
     * Set nodes to a new array of nodes
     * @param Node[] $nodes
     */
    public function setNodes(array $nodes) : self
    {
        $this->nodes = $nodes;
        return $this;
    }

    /**
     * @param callable(Node) : Node $callback
     * @return $this
     */
    public function map($callback) : self
    {
        foreach ($this->nodes as &$node) {
            $node = $callback($node);
        }
        return $this;
    }


    // === HELPERS ===

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