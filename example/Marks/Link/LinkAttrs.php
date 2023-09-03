<?php

namespace Hyvor\Phrosemirror\Example\Marks\Link;

use Hyvor\Phrosemirror\Types\AttrsType;

class LinkAttrs extends AttrsType
{
    public string $href;
    public ?string $title = null;
}