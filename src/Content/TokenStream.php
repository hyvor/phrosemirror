<?php

namespace Hyvor\Phrosemirror\Content;

class TokenStream
{

    /** @var string[] */
    public array $tokens;

    public int $pos = 0;

    public function __construct(string $string)
    {
        $split = preg_split('/\s*(?=\b|\W|$)/', $string, flags: PREG_SPLIT_NO_EMPTY);
        if (!$split) {
            $this->tokens = [];
            return;
        }
        $this->tokens = $split;
        if ($this->tokens[count($this->tokens) - 1] === '') array_pop($this->tokens);
        if ($this->tokens[0] === '') array_shift($this->tokens);
    }

    public function next() : ?string
    {
        return $this->tokens[$this->pos] ?? null;
    }

    public function eat(string $token) : bool
    {
        if ($this->next() === $token) {
            $this->pos++;
            return true;
        }
        return false;
    }

}