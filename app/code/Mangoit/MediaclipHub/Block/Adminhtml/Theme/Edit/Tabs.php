<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml\Theme\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        
        parent::_construct();
        $this->setId('checkmodule_theme_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Theme Information'));
    }
}
