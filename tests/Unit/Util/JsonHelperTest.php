<?php


use Hyvor\Phrosemirror\Exception\InvalidJsonException;
use Hyvor\Phrosemirror\Util\JsonHelper;

it('returns an array correctly from array, object, and string', function() {

    expect(JsonHelper::getJsonArray([]))->toBe([]);
    expect(JsonHelper::getJsonArray((object) ['type' => 'doc']))->toBe(['type' => 'doc']);
    expect(JsonHelper::getJsonArray(json_encode(['type' => 'doc'])))->toBe(['type' => 'doc']);

});

it('when JSON decoding fails', function() {
    JsonHelper::getJsonArray('invalid');
})->throws(InvalidJsonException::class, 'Unable to decode JSON');