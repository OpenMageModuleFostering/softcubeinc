<?php

class SoftCube_Integration_Block_Customer extends Mage_Core_Block_Template
{
    public function getCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomerId();
    }
}