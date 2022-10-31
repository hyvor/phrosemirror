<?php

namespace Hyvor\Phrosemirror\Document;

use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Util\JsonHelper;


// just a wrapper around Node for better DX
class Document extends Node
{

    public static function fromJson(Schema $schema, $json): Node
    {

        $json = JsonHelper::getJsonArray($json);

        if (($json['type'] ?? null) !== 'doc')
            throw new InvalidJsonException('The top node must be a doc node');

        return parent::fromJson($schema, $json);
    }

}