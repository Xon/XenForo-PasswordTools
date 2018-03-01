<?php

class KL_PasswordTools_Installer
{
    public static function install(/** @noinspection PhpUnusedParameterInspection */ $existingAddOn, array $addOnData, SimpleXMLElement $xml)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $required = '5.4.0';
        $phpversion = phpversion();
        if (version_compare($phpversion, $required, '<'))
        {
            throw new XenForo_Exception(
                "PHP {$required} or newer is required. {$phpversion} does not meet this requirement. Please ask your host to upgrade PHP",
                true
            );
        }
        if (XenForo_Application::$versionId < 1030070)
        {
            throw new XenForo_Exception('XenForo 1.3.0+ is Required!', true);
        }

        $addonsToUninstall = array('jQueryComplexify','kl_password_tools');
        /** @var XenForo_Model_AddOn $addonModel */
        $addonModel = XenForo_Model::create("XenForo_Model_AddOn");
        foreach($addonsToUninstall as $addonToUninstall)
        {
            $addon = $addonModel->getAddOnById($addonToUninstall);
            if (!empty($addon))
            {
                $dw = XenForo_DataWriter::create('XenForo_DataWriter_AddOn');
                $dw->setExistingData($addonToUninstall);
                $dw->delete();
            }
        }
    }
}
