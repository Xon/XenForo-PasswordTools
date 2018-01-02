<?php

/**
 * KL_PasswordTools_Install
 *
 *	@author: Katsulynx
 *  @last_edit:	22.08.2015
 */

class KL_PasswordTools_Install {
    
    /*
     * TYPE: VARIABLE
     * PURPOSE: UNINSTALL JQUERY COMPLEXIFY
     * @last_edit: 22.08.2015
     */
    protected static $_addonsToUninstall = array('jQueryComplexify');
    	
    /*
     * TYPE: INSTALL
     * PURPOSE: INSTALL/UPGRADE ADDON
     * @last_edit: 22.08.2015
     */
    public static function install() {
        $addonModel = XenForo_Model::create("XenForo_Model_AddOn");
        $dw         = XenForo_DataWriter::create('XenForo_DataWriter_AddOn');
        
        foreach(self::$_addonsToUninstall as $addonToUninstall) {
            $addon = $addonModel->getAddOnById($addonToUninstall);
            if (!empty($addon)) {
                $dw->setExistingData($addonToUninstall);
                $dw->delete();
            }
        }
    }
}