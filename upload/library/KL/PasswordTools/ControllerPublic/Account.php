<?php

/**
 * KL_PasswordTools_ControllerPublic_Account
 *
 *	@author: Katsulynx
 *  @last_edit:	12/11/2016
 */

class KL_PasswordTools_ControllerPublic_Account extends XFCP_KL_PasswordTools_ControllerPublic_Account {
	public function actionSecurity() {
		$return = parent::actionSecurity();
		
		if($return instanceof XenForo_ControllerResponse_View) {
			$option = XenForo_Application::get('options');
			if($option->KL_PasswordForceUpdate) {
				$date = XenForo_Visitor::getInstance()->password_date + $option->KL_PasswordUpdateCycle * 86400;

				$return->subView->params['kl_isForced'] = $date < XenForo_Application::$time;
				$return->subView->params['kl_forcedIn'] = floor(abs(($date - XenForo_Application::$time)) / 86400);
			}
		}
		
		return $return;
	}
}