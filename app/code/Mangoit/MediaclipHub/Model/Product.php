<?php
/**
 * Copyright © 2015 Mangoit. All rights reserved.
 */

namespace Mangoit\MediaclipHub\Model;

use Magento\Framework\Exception\ProductException;

/**
 * Producttab product model
 */
class Product extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Mangoit\MediaclipHub\Model\ResourceModel\Product');
    }
    public function getMediaClipProductByLabel($label, $_module){
        $collection = $this->getCollection();
        if ($label) {
            $collection->addFieldToFilter('product_label', $label);
        }
        if ($_module) {
            $collection->addFieldToFilter('module', $_module);
        }
        return $collection->getData();
    }

    public function getMediaClipProductBySku($sku, $module){
        $collection = $this->getCollection();
        if ($sku) {
            $collection->addFieldToFilter('plu', $sku);
            $collection->addFieldToFilter('module', $module);
        }

        return $collection->getData();
    }
}
