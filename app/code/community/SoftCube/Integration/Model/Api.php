<?php

class SoftCube_Integration_Model_Api extends Mage_Core_Model_Abstract
{
    protected $_services = array();

    public function getService($serviceName)
    {
        if (isset($this->_services[$serviceName])) {
            return $this->_services[$serviceName];
        }

        try {

            $serviceModel = Mage::getModel('softcube_integration/service_' . $serviceName);
            if (!$serviceModel) {
                Mage::throwException(Mage::helper('core/translate')->__('Cant\' retrieve service model'));
            }
            $this->_services[$serviceName] = $serviceModel;
            return $this->_services[$serviceName];

        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::getModel('adminhtml/session')->addError(Mage::helper('core/translate')->__($e->getMessage()));
            return $this;
        }
    }
}