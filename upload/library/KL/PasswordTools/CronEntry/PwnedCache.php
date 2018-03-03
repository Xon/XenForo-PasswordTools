<?php

class  KL_PasswordTools_CronEntry_PwnedCache
{
	/**
	 * Deletes expired pwned hash cache.
	 */
	public static function deleteExpiredHashes()
	{
        $db = XenForo_Application::getDb();
        $cutoff = null;
        $pwnedPasswordCacheTime = intval(XenForo_Application::getOptions()->pwnedPasswordCacheTime);
        if ($pwnedPasswordCacheTime > 0)
        {
            $cutoff = XenForo_Application::$time - $pwnedPasswordCacheTime * 86400;
        }
        if ($cutoff)
        {
            $db->query('delete from xf_sv_pwned_hash_cache where last_update < ?', $cutoff);
        }
	}
}