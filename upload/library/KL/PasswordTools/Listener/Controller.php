<?php

/**
 * KL_PasswordTools_Listener_Controller
 *
 *	@author: Katsulynx
 *  @last_edit:	12/11/2016
 */

class KL_PasswordTools_Listener_Controller {
    public static function extend($class, array &$extend) {
		if(!class_exists('XFCP_KL_PasswordTools_Controller', false)) {
        	$extend[] = 'KL_PasswordTools_Controller';
		}
    }
}