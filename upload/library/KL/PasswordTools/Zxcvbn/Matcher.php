<?php

/**
 * KL_PasswordTools_Matcher
 *
 *	@author: Ben Jeavons, Katsulynx
 *  @last_edit:	21.08.2015
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
            $matcher = get_class($this).'s_'.$matcher.'Match';
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
    protected function getMatchers() {
        return array('Date','Digit','L33t','Repeat','Sequence','Spatial','Year','Dictionary');
    }
}