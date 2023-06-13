<?php

namespace Hyvor\Phrosemirror\Test\Unit\Util;

use DOMDocument;
use Hyvor\Phrosemirror\Util\InlineStyle;

it('gets attributes', function() {

    $doc = new DOMDocument();
    $el = $doc->createElement('div');
    $el->setAttribute('style', 'color: red; background: blue;');

    $attrs = InlineStyle::get($el);

    expect($attrs)->toBe([
        'color' => 'red',
        'background' => 'blue'
    ]);

});


it('gets attributes without ending semicolon', function() {

    $doc = new DOMDocument();
    $el = $doc->createElement('div');
    $el->setAttribute('style', 'color: red; background: blue');

    $attrs = InlineStyle::get($el);

    expect($attrs)->toBe([
        'color' => 'red',
        'background' => 'blue'
    ]);

});

it('gets an attribute', function() {

    $doc = new DOMDocument();
    $el = $doc->createElement('div');
    $el->setAttribute('style', 'color: red; background: blue');

    $attrs = InlineStyle::getAttribute($el, 'color');

    expect($attrs)->toBe('red');

});