<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @package FigDice
 */

namespace figdice\exceptions;

class LexerUnexpectedCharException extends FigException {
    /**
     * @param string $char
     * @param int $position
     * @param string $expression
     *
     * @internal param string $message
     */
    public function __construct($char, $position, $expression) {
		parent::__construct("Unexpected character: $char at position: $position in expression: '$expression'.");
	}
}
