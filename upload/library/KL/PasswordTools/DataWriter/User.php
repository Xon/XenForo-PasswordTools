<?php

/**
 * KL_PasswordTools_DataWriter_User
 *
 *	@author: Katsulynx
 *  @last_edit:	15.08.2015
 */

class KL_PasswordTools_DataWriter_User extends XFCP_KL_PasswordTools_DataWriter_User {
	public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false) {
        if ($requirePassword) {
            $pattern    = $password;
            $zxcvbn     = new KL_PasswordTools_Zxcvbn;
            $options    = XenForo_Application::get('options');
            $rejected   = false;

            $requirements = array(
                'minimum_score'     => $options->KL_PasswordStrengthMeter_str,
                'minimum_length'    => $options->KL_PasswordStrengthMeter_min,
                'force_reject'      => $options->KL_PasswordStrengthMeter_force,
                'blacklist'         => array_merge($options->KL_PasswordStrengthMeter_blacklist, array($options->boardTitle))
            );

            if(strlen($pattern) < $requirements['minimum_length']) {
                $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooShort'), 'password');
                return false;
            }

            $pwd_result = $zxcvbn->passwordStrength($pattern, $requirements['blacklist']);

            if($pwd_result['score'] < $requirements['minimum_score']) {
                $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooWeak'), 'password');
                return false;
            }

            if($requirements['force_reject']) {
                foreach($pwd_result['match_sequence'] as $match_sequence) {
                    if($match_sequence['dictionary_name'] === 'user_inputs') {
                        $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_errorInvalidExpression'), 'password');
                        return false;
                    }
                }
            }
        }
        
        return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
    }
}