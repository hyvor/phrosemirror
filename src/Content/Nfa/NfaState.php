<?php

namespace Hyvor\Phrosemirror\Content\Nfa;

use Hyvor\Phrosemirror\Types\NodeType;

class NfaState
{

    public bool $isEnd;

    /**
     * @var array<string, NfaState>
     */
    public array $transition = [];

    /**
     * @var NfaState[]
     */
    public array $epsilonTransitions = [];

    public function __construct(bool $isEnd)
    {
        $this->isEnd = $isEnd;
    }

    public function addEpsilonTransition(NfaState $to) : self
    {
        $this->epsilonTransitions[] = $to;
        return $this;
    }

    public function addTransition(NfaState $to, NodeType $type) : self
    {
        $this->transition[$type->name] = $to;
        return $this;
    }

}