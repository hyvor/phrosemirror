<?php

namespace Hyvor\Phrosemirror\Util;

use DOMElement;

class InlineStyle
{

    /**
     * @return array<string, string>
     */
    public static function get(DOMElement $el): array
    {
        $results = [];

        $style = $el->getAttribute('style');

        preg_match_all(
            "/([\w-]+)\s*:\s*([^;]+)\s*;?/",
            $style,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $results[strval($match[1])] = strval($match[2]);
        }

        return $results;
    }

    /**
     * @param string|string[] $value
     */
    public static function hasAttribute(DOMElement $el, string|array $value): bool
    {
        $styles = self::get($el);

        if (is_string($value)) {
            return in_array($value, array_keys($styles));
        }

        if (is_array($value)) {
            return array_diff($value, $styles) == [];
        }
    }

    public static function getAttribute(DOMElement $el, string $attribute): ?string
    {
        return self::get($el)[$attribute] ?? null;
    }
}
