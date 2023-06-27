<?php

namespace Hyvor\Phrosemirror\Content;

use Hyvor\Phrosemirror\Content\Nfa\Nfa;
use Hyvor\Phrosemirror\Content\Nfa\NfaState;
use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Types\Schema;

/**
 * Sanitizes a document to make it compliant
 * with the schema and its content expressions
 * It also tries to wrap uncompliant nodes when possible
 */
class Sanitizer
{

    public static function sanitize(Schema $schema, Node $doc) : Node
    {
        return (new self($schema, $doc))->getSanitizedDocument();
    }

    public function __construct(
        private readonly Schema $schema,
        private readonly Node $doc,
    ) {}

    public function getSanitizedDocument() : Node
    {
        $this->sanitizeNode($this->doc);
        return $this->doc;
    }

    private function sanitizeNode(Node $node) : void
    {

        $expr = ContentExpression::getExpr($node->type->content, $this->schema);
        $content = $node->content;

        if (!$expr) {
            $node->content = new Fragment([]);
            return;
        }

        $nfa = Nfa::fromExpr($expr);

        $currentStates = [];
        $this->addNextState($nfa->start, $currentStates, []);

        foreach ($content as $node) {
            $nextStates = [];
            foreach ($currentStates as $state) {
                $nextState = $state->transition[$node->type->name] ?? null;
                if ($nextState) {
                    $this->addNextState($nextState, $nextStates, []);
                }
            }
            $currentStates = $nextStates;
        }

        dd($currentStates);

    }


    /**
     * @param NfaState $state
     * @param NfaState[] $nextStates
     * @param NfaState[] $visited
     */
    private function addNextState(NfaState $state, array &$nextStates, array $visited) : void
    {
        if (count($state->epsilonTransitions)) {
            foreach ($state->epsilonTransitions as $st) {
                if (!in_array($st, $visited)) {
                    $visited[] = $st;
                    $this->addNextState($st, $nextStates, $visited);
                }
            }
        } else {
            $nextStates[] = $state;
        }
    }

}