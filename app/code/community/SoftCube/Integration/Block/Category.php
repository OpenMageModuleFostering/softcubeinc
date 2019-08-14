<?php

class SoftCube_Integration_Block_Category extends Mage_Core_Block_Template
{
    public function getCategoryId()
    {
        if ($category = Mage::registry('current_category')) {
            return $category->getId();
        }
        return 0;
    }
}