<?php

namespace Hyvor\Phrosemirror\Content\Nfa;

use Hyvor\Phrosemirror\Content\Expr\ChoiceExpr;
use Hyvor\Phrosemirror\Content\Expr\Expr;
use Hyvor\Phrosemirror\Content\Expr\NodeTypeExpr;
use Hyvor\Phrosemirror\Content\Expr\OptionalExpr;
use Hyvor\Phrosemirror\Content\Expr\PlusExpr;
use Hyvor\Phrosemirror\Content\Expr\RangeExpr;
use Hyvor\Phrosemirror\Content\Expr\SequenceExpr;
use Hyvor\Phrosemirror\Content\Expr\StarExpr;
use Hyvor\Phrosemirror\Exception\ContentExpressionException;
use Hyvor\Phrosemirror\Types\NodeType;

class Nfa
{

    public function __construct(
        public NfaState $start,
        public NfaState $end
    ) {}

    /*public static function fromEpsilon() : self
    {
        $start = new NfaState(false);
        $end = new NfaState(true);
        $start->addEpsilonTransition($end);

        return new Nfa($start, $end);
    }*/

    public static function fromType(NodeType $type) : self
    {
        $start = new NfaState(false);
        $end = new NfaState(true);
        $start->addTransition($end, $type);

        return new Nfa($start, $end);
    }

    public static function concat(Nfa $first, Nfa $second) : self
    {
        $first->end->addEpsilonTransition($second->start);
        $first->end->isEnd = false;
        return new Nfa($first->start, $second->end);
    }

    public static function union(Nfa $first, Nfa $second) : self
    {
        $start = new NfaState(false);
        $start->addEpsilonTransition($first->start);
        $start->addEpsilonTransition($second->start);

        $end = new NfaState(true);
        $first->end->addEpsilonTransition($end);
        $first->end->isEnd = false;

        $second->end->addEpsilonTransition($end);
        $second->end->isEnd = false;

        return new Nfa($start, $end);
    }

    public static function closure(Nfa $nfa) : self
    {
        $start = new NfaState(false);
        $end = new NfaState(true);

        $start->addEpsilonTransition($end);
        $start->addEpsilonTransition($nfa->start);

        $nfa->end->addEpsilonTransition($end);
        $nfa->end->addEpsilonTransition($nfa->start);
        $nfa->end->isEnd = false;

        return new Nfa($start, $end);
    }

    public static function optional(Nfa $nfa) : self
    {
        $start = new NfaState(false);
        $end = new NfaState(true);

        $start->addEpsilonTransition($end);
        $start->addEpsilonTransition($nfa->start);

        $nfa->end->addEpsilonTransition($end);
        $nfa->end->isEnd = false;

        return new Nfa($start, $end);
    }

    public static function plus(Nfa $nfa) : self
    {
        $start = new NfaState(false);
        $end = new NfaState(true);

        $start->addEpsilonTransition($nfa->start);

        $nfa->end->addEpsilonTransition($end);
        $nfa->end->addEpsilonTransition($nfa->start);
        $nfa->end->isEnd = false;

        return new Nfa($start, $end);
    }

    public static function fromExpr(Expr $expr) : self
    {

        if ($expr instanceof ChoiceExpr) {
            $last = null;
            foreach ($expr->exprs as $choice) {
                $nfa = self::fromExpr($choice);
                if ($last === null) {
                    $last = $nfa;
                } else {
                    $last = self::union($last, $nfa);
                }
            }
            if ($last) return $last;
        }

        if ($expr instanceof NodeTypeExpr) {
            return self::fromType($expr->type);
        }

        if ($expr instanceof SequenceExpr) {
            $last = null;
            foreach ($expr->exprs as $sequence) {
                $nfa = self::fromExpr($sequence);
                if ($last === null) {
                    $last = $nfa;
                } else {
                    $last = self::concat($last, $nfa);
                }
            }
            if ($last) return $last;
        }

        if ($expr instanceof OptionalExpr) {
            $nfa = self::fromExpr($expr->expr);
            return self::optional($nfa);
        }

        if ($expr instanceof PlusExpr) {
            $nfa = self::fromExpr($expr->expr);
            return self::plus($nfa);
        }

        if ($expr instanceof StarExpr) {
            $nfa = self::fromExpr($expr->expr);
            return self::closure($nfa);
        }

        if ($expr instanceof RangeExpr) {
            // convert range to sequence
            $exprs = [];
            foreach (range(1, $expr->min) as $i) {
                $exprs[] = $expr->expr;
            }
            if ($expr->max === -1) {
                $exprs[] = new StarExpr($expr->expr);
            } else {
                foreach (range($expr->min + 1, $expr->max) as $i) {
                    $exprs[] = new OptionalExpr($expr->expr);
                }
            }
            $expr = new SequenceExpr($exprs);
            return self::fromExpr($expr);
        }

        throw new ContentExpressionException('Invalid expression for NFA: ' . get_class($expr));

    }

}