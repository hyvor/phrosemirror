<?php

namespace Hyvor\Phrosemirror\Example\Nodes\Image;

use Hyvor\Phrosemirror\Types\AttrsType;

class ImageAttrs extends AttrsType
{
    public ?string $src;
    public ?string $alt = null;
    public ?string $title = null;
}