<?php

namespace Hyvor\Phrosemirror\Content;

use Hyvor\Phrosemirror\Content\Nfa\Nfa;
use Hyvor\Phrosemirror\Content\Nfa\NfaState;
use Hyvor\Phrosemirror\Document\Document;
use Hyvor\Phrosemirror\Document\Fragment;
use Hyvor\Phrosemirror\Document\Node;
use Hyvor\Phrosemirror\Document\TextNode;
use Hyvor\Phrosemirror\Exception\SanitizerException;
use Hyvor\Phrosemirror\Types\Schema;
use Hyvor\Phrosemirror\Util\DeepCopy;

/**
 * Sanitizes a document to make it compliant
 * with the schema and its content expressions
 * It also tries to wrap uncompliant nodes when possible
 */
class Sanitizer
{

    public static function sanitize(Schema $schema, Document $doc) : Document
    {
        return (new self($schema, $doc))->getSanitizedDocument();
    }

    public function __construct(
        private readonly Schema $schema,
        private readonly ?Document $doc = null,
    ) {}

    public function getSanitizedDocument() : Document
    {
        if (!$this->doc)
            throw new SanitizerException('Document is not set');

        $this->sanitizeNode($this->doc);
        return $this->doc;
    }

    private function sanitizeNode(Node $node) : void
    {

        $content = $node->content;
        $this->matchChildren($node, fix: true);

        foreach ($content as $child) {
            if ($child->content->count() > 0) {
                $this->sanitizeNode($child);
            }
        }

    }

    public function matchChildren(Node $node, bool $fix = false) : bool
    {

        $expr = ContentExpression::getExpr($node->type->content, $this->schema);

        if ($node instanceof TextNode) {
            return false;
        }

        if (!$expr) {
            if ($fix) {
                $node->content = new Fragment([]);
            }
            return false;
        }

        $nfa = Nfa::fromExpr($expr);

        $currentStates = [];
        $this->addNextState($nfa->start, $currentStates, []);

        foreach ($node->content as $child) {
            $nextStates = [];
            $matched = false;

            $this->addNextStateFromCurrentStates($nextStates, $currentStates, $child, $matched);

            /**
             * Try to fix invalid content
             */
            $removed = false;
            if (!$matched) {
                if ($fix) {


                    $currentIndex = $node->content->getIndexOfNode($child);
                    $lastChild = $currentIndex && $currentIndex > 0 ?
                        $node->content->nth($currentIndex - 1) :
                        null;

                    if (
                        $child->type->inline &&
                        $lastChild &&
                        $this->copyInlineChildToLastNode($child, $lastChild)
                    ) {
                        $node->content->removeNode($child);
                        $removed = true;
                    } else if ($wrapper = $this->tryToWrap($currentStates, $node, $child)) {

                        /**
                         * We need to add next states as if we matched
                         * the NFA path of the wrapper node
                         */
                        $nextStates = [];
                        $this->addNextStateFromCurrentStates($nextStates, $currentStates, $wrapper);

                    } elseif (
                        count($node->content->all()) &&
                        $this->tryToPromoteGrandChildren($node, $child)
                    ) {
                        /**
                         * tryPromoteGrandChildren() matches all content after adding grandchildren
                         * So, no need to go to next states
                         */
                        return true;
                    } else {
                        // remove the node as the last resort
                        $node->content->removeNode($child);
                        $removed = true;
                    }


                } else {
                    return false;
                }
            }

            /**
             * If the element is removed, we don't need to update the current states
             * We will match the next element with the same states
             */
            if ($removed === false) {
                $currentStates = $nextStates;
            }
        }

        return $this->hasMatchingStates($currentStates);

    }

    /**
     * When $child is not matched inside $node,
     * We try to promote grand children to $node replacing the $child
     * If this matches, we return true
     */
    private function tryToPromoteGrandChildren(Node $node, Node $child) : bool
    {

        $index = $node->content->getIndexOfNode($child);
        if ($index === null)
            return false;

        $nodeCopy = DeepCopy::copy($node);
        $childCopy = DeepCopy::copy($child);

        $fragment = $nodeCopy->content;
        $nodeChildren = $fragment->all();

        $grandChildren = $childCopy->content->all();

        array_splice($nodeChildren, $index, 1, $grandChildren);
        $fragment->setNodes($nodeChildren);

        if ($this->matchChildren($nodeCopy)) {
            $node->content->setNodes($nodeCopy->content->all());
            return true;
        }

        return false;

    }

    private function copyInlineChildToLastNode(Node $child, Node $lastNode) : bool
    {

        $lastNodeCopy = clone $lastNode;
        $lastNodeCopy->content->addNode($child);

        /**
         * Only copy if the content is valid
         * when the last node is added
         */
        if ($this->matchChildren($lastNodeCopy)) {
            $lastNode->content->setNodes($lastNodeCopy->content->all());
            return true;
        }

        return false;

    }

    /**
     * Tries to wrap the node
     * only if doing so does not make the content invalid
     * @param NfaState[] $states
     */
    private function tryToWrap(array $states, Node $parent, Node $node) : ?Node
    {

        $index = $parent->content->getIndexOfNode($node);

        if ($index === null)
            return null;

        $possibleNodeTypeNames = [];

        foreach ($states as $state) {
            foreach ($state->transition as $name => $nextState) {
                if (!in_array($name, $possibleNodeTypeNames))
                    $possibleNodeTypeNames[] = $name;
            }
        }

        foreach ($possibleNodeTypeNames as $nodeTypeName) {

            $nodeType = $this->schema->getNodeTypeByName($nodeTypeName);

            if (!$nodeType)
                continue;

            $attrs = new $nodeType->attrs;

            /**
             * We need all properties to be optional to generate
             * this wrapper node
             */
            if (!$attrs->allPropertiesAreOptional())
                continue;

            $fragment = new Fragment([$node]);

            $wrapper = new Node(
                $nodeType,
                $attrs,
                $fragment,
            );

            $expr = ContentExpression::getExpr($nodeType->content, $this->schema);

            if (!$expr)
                continue;

            $parentCopy = DeepCopy::copy($parent);
            $parentCopy->content->setNodes(
                array_replace(
                    $parentCopy->content->all(),
                    [$index => $wrapper]
                )
            );

            /**
             * If parent's content is valid
             * parentCopy's content should be valid
             */
            if (
                $this->matchChildren($parent) &&
                !$this->matchChildren($parentCopy)
            ) {
                continue;
            }

            if ($this->matchChildren($wrapper)) {
                $parent->content->replaceNode($node, $wrapper);
                return $wrapper;
            }
        }

        return null;

    }

    /**
     * @param NfaState[] $states
     */
    private function hasMatchingStates(array $states) : bool
    {
        foreach ($states as $state) {
            if ($state->isEnd) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param NfaState[] $nextStates
     * @param NfaState[] $currentStates
     */
    private function addNextStateFromCurrentStates(
        array &$nextStates,
        array $currentStates,
        Node $node,
        bool &$matched = false
    ) : void
    {
        foreach ($currentStates as $state) {
            $nextState = $state->transition[$node->type->name] ?? null;
            if ($nextState) {
                $matched = true;
                $this->addNextState($nextState, $nextStates, []);
            }
        }
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