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
     * @param InputJsonType $json
     */
    public static function fromJson(Schema $schema, $json) : self
    {

        $json = JsonHelper::getJsonArray($json);

        if (!isset($json['type'])) {
            throw new InvalidJsonException('Mark type is not set in JSON');
        }

        $typeName = $json['type'];
        $type = $schema->getMakrTypeByName($typeName);

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