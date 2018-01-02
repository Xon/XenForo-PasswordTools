<?php

/**
 * KL_PasswordTools_Zxcvbn_Matchers_Bruteforce
 *
 *	@author: Ben Jeavons
 *  @last_edit:	15.08.2015
 */

class KL_PasswordTools_Zxcvbn_Matchers_Bruteforce extends KL_PasswordTools_Zxcvbn_Matchers_Match
{

    /**
     * @copydoc Match::match()
     */
    public static function match($password, array $userInputs = array())
    {
        // Matches entire string.
        $match = new static($password, 0, strlen($password) - 1, $password);
        return array($match);
    }

    /**
     * @param $password
     * @param $begin
     * @param $end
     * @param $token
     * @param $cardinality
     */
    public function __construct($password, $begin, $end, $token, $cardinality = null)
    {
        parent::__construct($password, $begin, $end, $token);
        $this->pattern = 'bruteforce';
        // Cardinality can be injected to support full password cardinality instead of token.
        $this->cardinality = $cardinality;
    }

    /**
     *
     */
    public function getEntropy()
    {
        if (is_null($this->entropy)) {
            $this->entropy = $this->log(pow($this->getCardinality(), strlen($this->token)));
        }
        return $this->entropy;
    }
}