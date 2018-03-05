<?php

class KL_PasswordTools_XenForo_DataWriter_User extends XFCP_KL_PasswordTools_XenForo_DataWriter_User
{
    public function setPassword($password, $passwordConfirm = false, XenForo_Authentication_Abstract $auth = null, $requirePassword = false)
    {
        $options = XenForo_Application::getOptions();

        if ($this->getOption(self::OPTION_ADMIN_EDIT) && !$options->enforcePasswordComplexityForAdmins)
        {
            return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
        }

        if ($requirePassword)
        {
            if (!empty($options->passwordToolsCheckTypes['zxcvbn']))
            {
                $this->sv_zxcvbnCheck($password);
                if ($this->getErrors())
                {
                    return false;
                }
            }
            if (!empty($options->passwordToolsCheckTypes['pwned']))
            {
                $this->sv_pwnedCheck($password);
                if ($this->getErrors())
                {
                    return false;
                }
            }
        }

        return parent::setPassword($password, $passwordConfirm, $auth, $requirePassword);
    }

    protected function sv_zxcvbnCheck($password)
    {
        /* Gather required stuff */
        $pattern = $password;
        $zxcvbn = new KL_PasswordTools_Zxcvbn();

        /* Gather requirements */
        $options = XenForo_Application::getOptions();
        $requirements = [
            'minimum_score'  => $options->KL_PasswordStrengthMeter_str,
            'minimum_length' => $options->KL_PasswordStrengthMeter_min,
            'force_reject'   => $options->KL_PasswordStrengthMeter_force,
            'blacklist'      => array_merge($options->KL_PasswordStrengthMeter_blacklist, [$options->boardTitle])
        ];

        /* Check against length */
        if (strlen($pattern) < $requirements['minimum_length'])
        {
            $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooShort'), 'password');

            return false;
        }

        /* Run Zxcvbn */
        $pwd_result = $zxcvbn->passwordStrength($pattern, $requirements['blacklist']);

        /* Check against score */
        if ($pwd_result['score'] < $requirements['minimum_score'])
        {
            $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_error_TooWeak'), 'password');

            return false;
        }

        /* Check for blacklist hit with force reject enabled */
        if ($requirements['force_reject'])
        {
            foreach ($pwd_result['match_sequence'] as $match_sequence)
            {
                if (isset($match_sequence->dictionaryName) && $match_sequence->dictionaryName === 'user_inputs')
                {
                    $this->error(new XenForo_Phrase('KL_PasswordStrengthMeter_errorInvalidExpression'), 'password');

                    return false;
                }
            }
        }

        return true;
    }

    public function sv_pwnedCheck($password)
    {
        $options = XenForo_Application::getOptions();
        $minimumUsages = intval($options->pwnedPasswordReuseCount);
        if ($minimumUsages < 1)
        {
            return true;
        }

        $hash = strtoupper(sha1($password));
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);
        $suffixSet = $this->getPwnedPrefixMatches($prefix);
        if ($suffixSet === null || $suffixSet === false)
        {
            return true;
        }
        if (isset($suffixSet[$suffix]) &&
            ($useCount = $suffixSet[$suffix]) &&
            $useCount >= $minimumUsages)
        {
            $this->error(new XenForo_Phrase('KL_pwned_password_x', ['count' => $useCount, 'countFormatted' => XenForo_Locale::numberFormat($useCount)]), 'password');
            return false;
        }

        return true;
    }

    protected function getPwnedPrefixMatches($prefix, $cutoff = null)
    {
        $db = $this->_db;
        if ($cutoff === null)
        {
            $pwnedPasswordCacheTime = intval(XenForo_Application::getOptions()->pwnedPasswordCacheTime);
            if ($pwnedPasswordCacheTime > 0)
            {
                $cutoff = XenForo_Application::$time - $pwnedPasswordCacheTime * 86400;
            }
        }
        $cutoff  = $cutoff ? $cutoff: 0;
        $suffixes = $db->fetchOne('select suffixes from xf_sv_pwned_hash_cache where prefix = ? and last_update > ?', [$prefix, $cutoff]);
        if ($suffixes)
        {
            $suffixSet = @json_decode($suffixes, true);
            if (is_array($suffixSet))
            {
                return $suffixSet;
            }
        }

        $suffixCount = [];
        try
        {
            $options = XenForo_Application::getOptions();
            $httpClient = XenForo_Helper_Http::getClient("https://api.pwnedpasswords.com/range/{$prefix}", [
                'useragent' => "XenForo/" . XenForo_Application::$version . " (" . $options->boardUrl . ')',
                'timeout' => 2,
            ]);
            $response = $httpClient->request('GET');
            if ($response->getStatus() == 200)
            {
                $text = $response->getBody();
                $suffixSet = array_filter(array_map('trim', explode("\n", $text)));

                foreach ($suffixSet as $suffix)
                {
                    $suffixInfo = explode(':', trim($suffix));
                    $suffixCount[$suffixInfo[0]] = intval($suffixInfo[1]);
                }
            }
            else
            {
                // API failed
                XenForo_Error::logException(new Exception('Pwned Password API failed;' . $response->getStatus() .' , '. $response->getBody()), false);
                return true;
            }
        }
        catch (Exception $e)
        {
            XenForo_Error::logException($e, false);

            return null;
        }

        $db->query('insert into xf_sv_pwned_hash_cache (prefix, suffixes, last_update)
          values (?,?,?)
          ON DUPLICATE KEY UPDATE
            suffixes = values(suffixes),
            last_update = values(last_update)
         ', [$prefix, json_encode($suffixCount) ,XenForo_Application::$time]);

        return $suffixCount;
    }
}

if (false)
{
    class XFCP_KL_PasswordTools_XenForo_DataWriter_User extends XenForo_DataWriter_User { }
}