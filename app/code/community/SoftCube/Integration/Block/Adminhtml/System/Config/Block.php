<?php

class SoftCube_Integration_Block_Adminhtml_System_Config_Block extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('softcube/integration/config/block.phtml');
    }

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return bool
     */
    public function isReadyToInstall()
    {
        return Mage::helper('softcube_integration')->validateBaseDir();
    }

    /**
     * @return bool
     */
    public function isAlreadyInstalled()
    {
        return Mage::helper('softcube_integration')->isAlreadyInstalled();
    }

    /**
     * @return string|null
     */
    public function getStoreKey()
    {
        return Mage::getStoreConfig(SoftCube_Integration_Model_Service_Softcube::XML_PATH_SOFTCUBE_STORE_KEY);
    }

    /**
     * Retrieve URL for install action
     *
     * @return string
     */
    public function getInstallActionUrl()
    {
        return $this->getUrl('*/softcube/install');
    }

    /**
     * Retrieve URL for reinstall action
     *
     * @return string
     */
    public function getReinstallActionUrl()
    {
        return $this->getUrl('*/softcube/reinstall');
    }


    /**
     * Retrieve URL for install action
     *
     * @return string
     */
    public function getUpdateActionUrl()
    {
        return $this->getUrl('*/softcube/updateInfo');
    }

}