<?php

/**
 * KL_PasswordTools_Listener_DataWriter
 *
 *	@author: Katsulynx
 *  @last_edit:	15.08.2015
 */

class KL_PasswordTools_Listener_DataWriter {
    public static function extend($class, array &$extend) {
        if (!class_exists('XFCP_KL_PasswordTools_DataWriter_User', false)) {
            $extend[] = 'KL_PasswordTools_DataWriter_User';
        }
    }
}