<?php

class SoftCube_Integration_Adminhtml_SoftcubeController extends Mage_Adminhtml_Controller_Action
{
    public function installAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        try {
            /** @var SoftCube_Integration_Model_Service_Cart $cartService */
            $cartService = Mage::getModel('softcube_integration/api')->getService('cart');
            /** @var SoftCube_Integration_Model_Service_Softcube $softCubeService */
            $softCubeService = Mage::getModel('softcube_integration/api')->getService('softcube');
            $softCubeService->getStoreKey();

            $bridgeUrl = $softCubeService->getBridgeUrl();
            $isBridgeDownloaded = Mage::helper('softcube_integration')->isBridgeDownloaded();

            if (!$isBridgeDownloaded && $bridgeUrl) {
                $cartService->downloadBridge($bridgeUrl);
            }

            $post = $this->getRequest()->getPost();
            if (empty($post)) {
                $post = $this->getRequest()->getParams();
            }

            $softCubeService->updateInformation($post);

            $softCubeService->getJs();

        } catch (Mage_Core_Exception $e) {
            $session->addError($this->__($e->getMessage()));
        }

        if (!isset($e)) {
            $session->addSuccess($this->__('Store was successfully registered'));
        }

        Mage::getModel('core/config')->removeCache();
    }

    public function reinstallAction()
    {
        /** @var Mage_Core_Model_Config $configModel */
        $configModel = Mage::getModel('core/config');

        $pathToRemove = array(
            SoftCube_Integration_Model_Service_Softcube::XML_PATH_SOFTCUBE_JS,
            SoftCube_Integration_Model_Service_Softcube::XML_PATH_SOFTCUBE_GUID,
            SoftCube_Integration_Model_Service_Abstract::XML_PATH_SOFTCUBE_STORE_KEY
        );

        foreach ($pathToRemove as $path) {
            $configModel->deleteConfig($path);
        }

        $configModel->removeCache();
        $this->_redirect('*/*/install', $this->getRequest()->getPost());
    }

    public function updateInfoAction()
    {
        $post = $this->getRequest()->getPost();
        /** @var SoftCube_Integration_Model_Service_Softcube $softCubeService */
        $softCubeService = Mage::getModel('softcube_integration/api')->getService('softcube');
        $session = Mage::getSingleton('adminhtml/session');
        try {
            $softCubeService->updateInformation($post);
        } catch (Mage_Core_Exception $e) {
            $session->addError($this->__($e->getMessage()));
        }

        if (!isset($e)) {
            $session->addSuccess($this->__('Information was successfully updated'));
        }

    }
}