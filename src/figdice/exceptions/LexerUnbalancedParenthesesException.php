<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @package FigDice
 */

namespace figdice\exceptions;

class LexerUnbalancedParenthesesException extends FigException {
    /**
     * LexerUnbalancedParenthesesException constructor.
     *
     * @param string $expression
     */
    public function __construct($expression) {
		parent::__construct('Unbalanced parentheses in expression: "' . $expression . '".');
	}
}
