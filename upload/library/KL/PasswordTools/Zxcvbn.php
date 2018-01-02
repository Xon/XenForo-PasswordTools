<?php

/**
 * KL_PasswordTools_Zxcvbn
 *
 *	@author: Ben Jeavons
 *  @last_edit:	15.08.2015
 */

class KL_PasswordTools_Zxcvbn
{
    /**
     * @var
     */
    protected $scorer;

    /**
     * @var
     */
    protected $searcher;

    /**
     * @var
     */
    protected $matcher;

    public function __construct()
    {
        $this->scorer   = new KL_PasswordTools_Zxcvbn_Scorer();
        $this->searcher = new KL_PasswordTools_Zxcvbn_Searcher();
        $this->matcher  = new KL_PasswordTools_Zxcvbn_Matcher();
    }

    /**
     * Calculate password strength via non-overlapping minimum entropy patterns.
     *
     * @param string $password
     *   Password to measure.
     * @param array $userInputs
     *   Optional user inputs.
     *
     * @return array
     *   Strength result array with keys:
     *     password
     *     entropy
     *     match_sequence
     *     score
     */
    public function passwordStrength($password, array $userInputs = array())
    {
        $timeStart = microtime(true);
        if (strlen($password) === 0) {
            $timeStop = microtime(true) - $timeStart;
            return $this->result($password, 0, array(), 0, array('calc_time' => $timeStop));
        }

        // Get matches for $password.
        $matches = $this->matcher->getMatches($password, $userInputs);

        // Calcuate minimum entropy and get best match sequence.
        $entropy = $this->searcher->getMinimumEntropy($password, $matches);
        $bestMatches = $this->searcher->matchSequence;

        // Calculate score and get crack time.
        $score = $this->scorer->score($entropy);
        $metrics = $this->scorer->getMetrics();

        $timeStop = microtime(true) - $timeStart;
        // Include metrics and calculation time.
        $params = array_merge($metrics, array('calc_time' => $timeStop));
        return $this->result($password, $entropy, $bestMatches, $score, $params);
    }

    /**
     * Result array.
     *
     * @param string $password
     * @param float $entropy
     * @param array $matches
     * @param int $score
     * @param array $params
     *
     * @return array
     */
    protected function result($password, $entropy, $matches, $score, $params = array()) {
        $r = array(
            'password'       => $password,
            'entropy'        => $entropy,
            'match_sequence' => $matches,
            'score'          => $score
        );
        return array_merge($params, $r);
    }

}