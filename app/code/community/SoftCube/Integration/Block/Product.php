<?php

class SoftCube_Integration_Block_Product extends Mage_Core_Block_Template
{
    /** @var  Mage_Catalog_Model_Product */
    protected $_product;

    /** @var array */
    private $_stockOptions = array(
        0 => 'OutOfStock',
        1 => 'InStock'
    );

    /** @var array */
    protected $_allowedProductTypes = array(
        Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        Mage_Catalog_Model_Product_Type::TYPE_BUNDLE,
        Mage_Catalog_Model_Product_Type::TYPE_GROUPED,
    );

    public function _construct()
    {
        parent::_construct();
        $this->_product = Mage::registry('current_product');
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        if ($this->_product) {
            return $this->_product->getId();
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        if ($this->_product) {
            $isInStock = (int)$this->_product->isInStock();
            return $this->__($this->_stockOptions[$isInStock]);
        }
        return $this->__($this->_stockOptions[0]);
    }

    /**
     * @return string
     */
    public function getVariantsIds()
    {
        $product = $this->_product;
        $productType = $product->getTypeId();

        $ids = array();
        if (in_array($productType, $this->_allowedProductTypes)) {
            $groupedIds = $product->getTypeInstance(true)->getChildrenIds($this->_product->getId(), false);
            foreach ($groupedIds as $value) {
                $ids = array_merge($ids, $value);
            }
            return implode(',', $ids);
        }

        return '';
    }
}