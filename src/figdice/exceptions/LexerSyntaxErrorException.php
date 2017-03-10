<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @package FigDice
 */

namespace figdice\exceptions;

class LexerSyntaxErrorException extends FigException {
    /**
     * LexerSyntaxErrorException constructor.
     *
     * @param string $message
     * @param string $expression
     */
    public function __construct($message, $expression) {
		parent::__construct($message . ' in expression: "' . $expression . '".');
	}
}
