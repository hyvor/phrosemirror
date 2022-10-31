<?php

namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Types\AttrsType;
use Hyvor\Phrosemirror\Types\MarkType;
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Util\JsonHelper;

class Mark
{

    use MarkNodeCommonTrait;

    public function __construct(

        public MarkType $type,

        public AttrsType $attrs,

    ) {}

    /**
     * @param Schema $schema
     */
    public static function fromJson(Schema $schema, mixed $json) : self
    {

        $json = JsonHelper::getJsonArray($json);

        if (!isset($json['type'])) {
            throw new InvalidJsonException('Mark type is not set in JSON');
        }

        if (!is_string($json['type'])) {
            throw new InvalidJsonException('Mark type should be a string');
        }

        $typeName = $json['type'];
        $type = $schema->getMarkTypeByName($typeName);

        if ($type === null) {
            throw new InvalidJsonException("Mark type $typeName not found in schema");
        }

        $jsonAttrs = $json['attrs'] ?? [];

        if (!is_array($jsonAttrs)) {
            throw new InvalidJsonException("Mark Attrs should be an array in $typeName");
        }

        $attrs = new $type->attrs;
        $attrs->setFromArray($jsonAttrs);

        return new self($type, $attrs);

    }

}