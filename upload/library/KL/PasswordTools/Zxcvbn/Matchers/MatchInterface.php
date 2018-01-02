<?php

/**
 * KL_PasswordTools_Zxcvbn_Matchers_MatchInterface
 *
 *	@author: Ben Jeavons
 *  @last_edit:	15.08.2015
 */

interface KL_PasswordTools_Zxcvbn_Matchers_MatchInterface
{

    /**
     * Match this password.
     *
     * @param string $password
     *   Password to check for match.
     * @param array $userInputs
     *   Array of values related to the user (optional).
     * @code
     *   array('Alice Smith')
     * @endcode
     * @return array
     *   Array of Match objects
     */
    public static function match($password, array $userInputs = array());

    /**
     * Get entropy for this match's token.
     *
     * @return float
     *   Entropy of the matched token in the password.
     */
    public function getEntropy();
}