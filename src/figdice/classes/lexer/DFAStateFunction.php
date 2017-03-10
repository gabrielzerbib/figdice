<?php
/**
 * @author Gabriel Zerbib <gabriel@figdice.org>
 * @package FigDice
 */

namespace figdice\classes\lexer;

use figdice\exceptions\LexerUnbalancedParenthesesException;

class DFAStateFunction extends DFAState {
	public function __construct() {
		parent::__construct();
	}
	/**
	 * @param Lexer $lexer
	 * @param string $char
	 */
	public function input(Lexer $lexer, $char) {
		if($char == ')') {
			$lexer->pushOperator(new TokenFunction($this->buffer, 0));
			$lexer->closeParenthesis();
		}
		else if($char == '(') {
			$lexer->pushOperator(new TokenFunction($this->buffer, 1));
			$lexer->pushOperator(new TokenLParen());
		}
		else if(self::isBlank($char)) {
			
		}
		else if($char == "'") {
			$lexer->pushOperator(new TokenFunction($this->buffer, 1));
			$lexer->setStateString();
		}
		else {
			$lexer->pushOperator(new TokenFunction($this->buffer, 1));
			$lexer->forwardInput($char);
		}
	}

	public function endOfInput($lexer) {
	    throw new LexerUnbalancedParenthesesException($lexer->getExpression());
	}
}
