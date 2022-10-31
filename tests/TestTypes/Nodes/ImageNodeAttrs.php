<?php

namespace Hyvor\Phrosemirror\Test\TestTypes\Nodes;

use Hyvor\Phrosemirror\Types\AttrsType;

class ImageNodeAttrs extends AttrsType
{

    public string $src;
    public ?string $alt;

}