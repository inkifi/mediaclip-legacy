<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mangoit\MediaclipHub\Block\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    public function getProductUrl($mediaclipProjectid = 0)
    {
        if ($this->_productUrl !== null) {
            return $this->_productUrl;
        }
        if ($this->getItem()->getRedirectUrl()) {
            return $this->getItem()->getRedirectUrl();
        }

        $product = $this->getProduct();
        $option = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
        
        // check if it is a mediaclip product
        if ($product->getAttributeSetId() != 4) {
            if ($mediaclipProjectid) {
                $additional = array('mode' => 'editCartProject', 'projectId' => "$mediaclipProjectid");
                $helper = $objectManager->create('Mangoit\MediaclipHub\Helper\Data');
                return $helper->getEditorUrl($product, $additional);
            }
        }
        return $product->getUrlModel()->getUrl($product);
    }
}
