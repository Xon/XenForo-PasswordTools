<?php

/**
 * KL_PasswordTools_ScorerInterface
 *
 *	@author: Ben Jeavons
 *  @last_edit:	15.08.2015
 */

interface KL_PasswordTools_Zxcvbn_ScorerInterface
{

    /**
     * Score for a password's bits of entropy.
     *
     * @param float $entropy
     *   Entropy to score.
     * @return float
     *   Score.
     */
    public function score($entropy);

    /**
     * Get metrics used to determine score.
     *
     * @return array
     *   Key value array of metrics.
     */
    public function getMetrics();
}