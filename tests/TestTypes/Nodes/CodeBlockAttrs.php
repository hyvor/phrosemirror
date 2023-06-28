<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Types\AttrsType;

class CodeBlockAttrs extends AttrsType
{

    public ?string $language = null;

}