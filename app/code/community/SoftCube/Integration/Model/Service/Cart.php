<?php

class SoftCube_Integration_Model_Service_Cart extends SoftCube_Integration_Model_Service_Abstract
{
    protected $_serviceName = 'cart';

    public function registerStore()
    {
        if (isset($this->_params['store_key'])) {
            return;
        }

        $result = $this->doRequest('bridge');
        if ($result) {
            $config = Mage::getModel('core/config');
            $config->saveConfig(static::XML_PATH_SOFTCUBE_STORE_KEY, $result->store_key);
            $config->cleanCache();
            $this->_params['store_key'] = $result->store_key;
        }

    }

    public function downloadBridge($bridgeUrl)
    {
        $file = $this->doRequest('bridge.download', array(), $bridgeUrl);
        Mage::getModel('softcube_integration/zip')->extractString($file);
    }

}
