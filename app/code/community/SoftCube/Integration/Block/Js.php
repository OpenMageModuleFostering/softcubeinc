<?php

class SoftCube_Integration_Block_Js extends Mage_Core_Block_Template
{
    const XML_PATH_SOFTCUBE_JS = 'softcube/general/js';

    public function getJs()
    {
        if ($js = Mage::getStoreConfig(static::XML_PATH_SOFTCUBE_JS)) {
            return $js;
        }
        return '';
    }
}