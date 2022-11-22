<?php
namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Converters\TextSerializer;
use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Types\AttrsType;
use Hyvor\Phrosemirror\Types\MarkType;
use Hyvor\Phrosemirror\Types\NodeType;
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Util\JsonHelper;


class Node
{

    use MarkNodeCommonTrait;

    public function __construct(

        public NodeType $type,

        // if null, and empty object will be placed
        public AttrsType $attrs = new AttrsType,

        public Fragment $content = new Fragment,

        /**
         * @var Mark[]
         */
        public array $marks = []

    ) {}


    public function toText() : string
    {
        $serializer = new TextSerializer;
        return $serializer->serialize($this);
    }


    /**
     * @param callable(Node) : void $closure
     * @return self
     */
    public function traverse(callable $closure) : self
    {

        $closure($this);

        foreach ($this->content->all() as $child) {
            $child->traverse($closure);
        }

        return $this;

    }

    /**
     * Check if the current node has a mark of the given type(s)
     * This only makes sense to use in Text Nodes
     *
     * @param class-string<MarkType>|class-string<MarkType>[] $type
     * @return bool
     */
    public function hasMark(string|array $type) : bool
    {

        $types = is_string($type) ? [$type] : $type;

        foreach ($this->marks as $mark) {
            if ($mark->isOfType($types))
                return true;
        }

        return false;

    }

    /**
     * @param class-string<NodeType>|class-string<NodeType>[]|null $types NodeTypes to select
     * @param bool $nested Whether to return nested children. If set to false, only direct children will be checked
     * @return array<Node>
     */
    public function getNodes(string|array|null $types = null, bool $nested = true) : array
    {

        $types = is_string($types) ? [$types] : $types;

        $nodes = [];

        foreach ($this->content as $node) {

            if ($types === null || $node->isOfType($types))
                $nodes[] = $node;

            if ($nested) {
                $nodes = [...$nodes, ...$node->getNodes($types)];
            }

        }

        return $nodes;

    }

    /**
     * @param class-string<MarkType>|class-string<MarkType>[]|null $types
     * @param bool $nested
     * @return array<Mark>
     */
    public function getMarks(string|array|null $types = null, bool $nested = true) : array
    {

        $types = is_string($types) ? [$types] : $types;

        $marks = [];

        // get current element's marks
        foreach ($this->marks as $mark) {
            if ($types === null || $mark->isOfType($types))
                $marks[] = $mark;
        }

        // get children's marks
        if ($nested) {
            foreach ($this->content as $child) {
                $marks = [...$marks, ...$child->getMarks($types)];
            }
        }

        return $marks;

    }

    /**
     * A concatenation of all nested text node values
     * @return string
     */
    public function allText() : string
    {

        if ($this instanceof TextNode)
            return $this->text;

        $text = '';
        foreach ($this->content as $child) {
            $text .= $child->allText();
        }

        return $text;

    }


    /**
     * @return array<string, mixed>
     */
    public function toArray() : array
    {

        $array = [
            'type' => $this->type->name,
        ];

        // text
        if ($this instanceof TextNode) {
            $array['text'] = $this->text;
        }

        $attrs = $this->attrs->toArray();

        if (count($attrs)) {
            $array['attrs'] = $attrs;
        }

        if (count($this->marks)) {
            $marksArray = [];

            foreach ($this->marks as $mark) {
                $marksArray[] = $mark->toArray();
            }

            $array['marks'] = $marksArray;
        }

        $children = $this->content->all();
        if (count($children)) {

            $contentArray = [];

            foreach ($children as $child) {
                $contentArray[] = $child->toArray();
            }

            $array['content'] = $contentArray;
        }

        return $array;

    }


    /**
     * @param mixed $json
     * @return self
     */
    public static function fromJson(Schema $schema, $json) : self
    {

        $json = JsonHelper::getJsonArray($json);

        if (!isset($json['type'])) {
            throw new InvalidJsonException('Node type is not set in JSON');
        }

        if (!is_string($json['type'])) {
            throw new InvalidJsonException('Node type should be a string in JSON');
        }

        $typeName = $json['type'];

        $type = $schema->getNodeTypeByName($typeName);

        if ($type === null) {
            throw new InvalidJsonException("Node type $typeName not found in schema");
        }

        $jsonAttrs = $json['attrs'] ?? [];

        if (!is_array($jsonAttrs)) {
            throw new InvalidJsonException("Node Attrs should be an array in $typeName");
        }

        $attrs = new $type->attrs;
        $attrs->setFromArray($jsonAttrs);

        $jsonContent = $json['content'] ?? [];

        if (!is_array($jsonContent)) {
            throw new InvalidJsonException("Node content should be an array in $typeName");
        }

        $contentFragment = Fragment::fromJson($schema, $jsonContent);

        $jsonMarks = $json['marks'] ?? [];

        if (!is_array($jsonMarks)) {
            throw new InvalidJsonException("Node marks should be an array in $typeName");
        }

        $marks = [];
        foreach ($jsonMarks as $jsonMark) {
            $marks[] = Mark::fromJson($schema, $jsonMark);
        }

        return $type->isText() ?
            new TextNode($type, $attrs, $json['text'] ?? '', $marks) :
            new self($type, $attrs, $contentFragment, $marks);

    }

}