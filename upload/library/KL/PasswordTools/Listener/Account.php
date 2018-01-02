<?php

/**
 * KL_PasswordTools_Listener_Account
 *
 *	@author: Katsulynx
 *  @last_edit:	12/11/2016
 */

class KL_PasswordTools_Listener_Account {
    public static function extend($class, array &$extend) {
        $extend[] = 'KL_PasswordTools_ControllerPublic_Account';
    }
}