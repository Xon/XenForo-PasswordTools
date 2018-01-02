<?php

/**
 * KL_PasswordTools_Matcher
 *
 *	@author: Ben Jeavons
 *  @last_edit:	15.08.2015
 */

class KL_PasswordTools_Zxcvbn_Matcher
{

    /**
     * Get matches for a password.
     *
     * @param string $password
     *   Password string to match.
     * @param array $userInputs
     *   Array of values related to the user (optional).
     * @code
     *   array('Alice Smith')
     * @endcode
     * @return array
     *   Array of Match objects.
     */
    public function getMatches($password, array $userInputs = array())
    {
        $matches = array();
        foreach ($this->getMatchers() as $matcher) {
            $matched = $matcher::match($password, $userInputs);
            if (is_array($matched) && !empty($matched)) {
                $matches = array_merge($matches, $matched);
            }
        }
        return $matches;
    }

    /**
     * Load available Match objects to match against a password.
     *
     * @return array
     *   Array of classes implementing MatchInterface
     */
    protected function getMatchers()
    {
        // @todo change to dynamic
        return array(
            'KL_PasswordTools_Zxcvbn_Matchers_DateMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_DigitMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_L33tMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_RepeatMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_SequenceMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_SpatialMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_YearMatch',
            'KL_PasswordTools_Zxcvbn_Matchers_DictionaryMatch',
        );
    }
}