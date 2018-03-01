<?php

/**
 * KL_PasswordTools_Controller
 *
 *	@author: Katsulynx
 *  @last_edit:	12/11/2016
 */

class KL_PasswordTools_Controller extends XFCP_KL_PasswordTools_Controller {
	protected function _preDispatch($action) {
		if(!strpos(get_parent_class(get_parent_class()), 'ControllerAdmin') && $action !== 'Security') {
			$option = XenForo_Application::get('options');
			$visitor = XenForo_Visitor::getInstance();
			$date = $visitor->password_date + $option->KL_PasswordUpdateCycle * 86400;
			if($visitor->user_id && $option->KL_PasswordForceUpdate && $date < XenForo_Application::$time) {
				throw $this->responseException($this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
											  XenForo_Link::buildPublicLink('full:account/security')));
			}
		}
		
		return parent::_preDispatchFirst($action);
	}
}