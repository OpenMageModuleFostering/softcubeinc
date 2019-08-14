<?php

class SoftCube_Integration_Block_Success extends Mage_Core_Block_Template
{
    public function getLastOrderId()
    {
        if ($orderId = Mage::getSingleton('checkout/session')->getLastOrderId()) {
            return $orderId;
        }

        return 0;
    }

    public function getLastOrderIncrementId()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($this->getLastOrderId());
        if ($order->getId()) {
            return $order->getIncrementId();
        }

        return 0;
    }
}