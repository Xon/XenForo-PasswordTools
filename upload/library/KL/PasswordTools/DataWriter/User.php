<?php

/**
 * KL_PasswordTools_DataWriter_User
 *
 *	@author: Katsulynx
 *  @last_edit:	21.08.2015
 */

class KL_PasswordTools_DataWriter_User extends XFCP_KL_PasswordTools_DataWriter_User {
	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false) {
        if ($requirePassword) {
            /* Gather required stuff */
            $pattern    = $password;
            $zxcvbn     = new KL_PasswordTools_Zxcvbn();
            $options    = XenForo_Application::get('options');

            /* Gather requirements */
            $requirements = array(
                'minimum_score'     => $options->KL_PasswordStrengthMeter_str,
                'minimum_length'    => $options->KL_PasswordStrengthMeter_min,
                'force_reject'      => $options->KL_PasswordStrengthMeter_force,
                'blacklist'         => array_merge($options->KL_PasswordStrengthMeter_blacklist, array($options->boardTitle))
            );

            /* Check against length */
            if(strlen($pattern) < $requirements['minimum_length']) {
                $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooShort'), 'password');
                return false;
            }

            /* Run Zxcvbn */
            $pwd_result = $zxcvbn->passwordStrength($pattern, $requirements['blacklist']);

            /* Check against score */
            if($pwd_result['score'] < $requirements['minimum_score']) {
                $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooWeak'), 'password');
                return false;
            }

            /* Check for blacklist hit with force reject enabled */
            if($requirements['force_reject']) {
                foreach($pwd_result['match_sequence'] as $match_sequence) {
                    if(isset($match_sequence->dictionaryName) && $match_sequence->dictionaryName === 'user_inputs') {
                        $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_errorInvalidExpression'), 'password');
                        return false;
                    }
                }
            }
        }

        /* Let the parent work */
        return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
    }
}

if (false)
{
    class XFCP_KL_PasswordTools_DataWriter_User extends XenForo_DataWriter_User {}
}
