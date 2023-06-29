<?php

namespace Hyvor\Phrosemirror\Test\Unit\Content;

use Hyvor\Phrosemirror\Content\ContentExpression;
use Hyvor\Phrosemirror\Content\Expr\ChoiceExpr;
use Hyvor\Phrosemirror\Content\Expr\NodeTypeExpr;
use Hyvor\Phrosemirror\Content\Expr\OptionalExpr;
use Hyvor\Phrosemirror\Content\Expr\PlusExpr;
use Hyvor\Phrosemirror\Content\Expr\RangeExpr;
use Hyvor\Phrosemirror\Content\Expr\SequenceExpr;
use Hyvor\Phrosemirror\Content\Expr\StarExpr;
use Hyvor\Phrosemirror\Exception\ContentExpressionException;

it('single', function() {
    $single = new ContentExpression('paragraph', schema());
    expect($single->expr)->toBeInstanceOf(NodeTypeExpr::class);
    expect($single->expr->type->name)->toBe('paragraph');
});

it('choice', function() {
    $choice = new ContentExpression('paragraph|heading', schema());
    expect($choice->expr)->toBeInstanceOf(ChoiceExpr::class);
    expect($choice->expr->exprs)->toHaveCount(2);
    expect($choice->expr->exprs[0]->type->name)->toBe('paragraph');
    expect($choice->expr->exprs[1]->type->name)->toBe('heading');
});

it('optional', function() {
   $optional = new ContentExpression('paragraph?', schema());
   expect($optional->expr)->toBeInstanceOf(OptionalExpr::class);
   expect($optional->expr->expr->type->name)->toBe('paragraph');
});

it('plus', function() {
    $expr = new ContentExpression('paragraph+', schema());
    expect($expr->expr)->toBeInstanceOf(PlusExpr::class);
    expect($expr->expr->expr->type->name)->toBe('paragraph');
});

it('range', function() {
    $expr = new ContentExpression('paragraph{1,3}', schema());
    expect($expr->expr)->toBeInstanceOf(RangeExpr::class);
    expect($expr->expr->expr->type->name)->toBe('paragraph');
    expect($expr->expr->min)->toBe(1);
    expect($expr->expr->max)->toBe(3);
});

it('range without second', function() {
    $expr = new ContentExpression('paragraph{1,}', schema());
    expect($expr->expr)->toBeInstanceOf(RangeExpr::class);
    expect($expr->expr->expr->type->name)->toBe('paragraph');
    expect($expr->expr->min)->toBe(1);
    expect($expr->expr->max)->toBe(-1);
});

it('range unclosed', function() {
    new ContentExpression('paragraph{1', schema());
})->throws(ContentExpressionException::class, 'Unclosed braced range expression');

it('star', function() {
    $expr = new ContentExpression('paragraph*', schema());
    expect($expr->expr)->toBeInstanceOf(StarExpr::class);
    expect($expr->expr->expr->type->name)->toBe('paragraph');
});

it('sequence', function() {
    $expr = new ContentExpression('paragraph heading', schema());
    expect($expr->expr)->toBeInstanceOf(SequenceExpr::class);
    expect($expr->expr->exprs)->toHaveCount(2);
    expect($expr->expr->exprs[0]->type->name)->toBe('paragraph');
    expect($expr->expr->exprs[1]->type->name)->toBe('heading');
});

it('brakcets', function() {
    $expr = new ContentExpression('paragraph (heading | code_block)+', schema());
    expect($expr->expr)->toBeInstanceOf(SequenceExpr::class);
    expect($expr->expr->exprs)->toHaveCount(2);

    expect($expr->expr->exprs[0]->type->name)->toBe('paragraph');

    expect($expr->expr->exprs[1])->toBeInstanceOf(PlusExpr::class);
    expect($expr->expr->exprs[1]->expr)->toBeInstanceOf(ChoiceExpr::class);
    expect($expr->expr->exprs[1]->expr->exprs)->toHaveCount(2);
    expect($expr->expr->exprs[1]->expr->exprs[0]->type->name)->toBe('heading');
    expect($expr->expr->exprs[1]->expr->exprs[1]->type->name)->toBe('code_block');
});

it('fails when not closing brackets', function() {
    new ContentExpression('paragraph (heading | code_block+', schema());
})->throws(ContentExpressionException::class, 'Missing closing parenthesis');

it('groups', function() {
   $expr = new ContentExpression('block+', schema());
    expect($expr->expr)->toBeInstanceOf(PlusExpr::class);
    expect($expr->expr->expr)->toBeInstanceOf(ChoiceExpr::class);

    expect($expr->expr->expr->exprs)->toHaveCount(3);

    expect($expr->expr->expr->exprs[0]->type->name)->toBe('paragraph');
    expect($expr->expr->expr->exprs[1]->type->name)->toBe('blockquote');
    expect($expr->expr->expr->exprs[2]->type->name)->toBe('heading');
});

it('empty', function() {
   $expr = new ContentExpression(null, schema());
   expect($expr->expr)->toBeNull();
});