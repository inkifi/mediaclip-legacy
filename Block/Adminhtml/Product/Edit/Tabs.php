<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        
        parent::_construct();
        $this->setId('checkmodule_product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product Information'));
    }
}
