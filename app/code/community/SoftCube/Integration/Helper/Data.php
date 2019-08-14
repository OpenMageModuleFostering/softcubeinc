<?php

class SoftCube_Integration_Helper_Data extends Mage_Core_Helper_Abstract
{

    const SOFTCUBE_BRIDGE_FILE = '/bridge2cart/bridge.php';

    /**
     * Check is root magento directory is writable
     * @return bool
     */
    public function validateBaseDir()
    {
        return is_writable(Mage::getBaseDir());
    }

    /**
     * @return bool
     */
    public function isAlreadyInstalled()
    {
        if (Mage::getStoreConfig(SoftCube_Integration_Model_Service_Softcube::XML_PATH_SOFTCUBE_JS)) {
            return true;
        }

        return false;
    }

    public function isBridgeDownloaded()
    {
        return is_readable(Mage::getBaseDir() . static::SOFTCUBE_BRIDGE_FILE);
    }
}