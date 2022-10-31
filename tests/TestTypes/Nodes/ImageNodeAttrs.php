<?php

namespace Hyvor\Prosemirror\Test\TestTypes\Nodes;

use Hyvor\Prosemirror\Types\AttrsType;

class ImageNodeAttrs extends AttrsType
{

    public string $src;
    public ?string $alt;

}