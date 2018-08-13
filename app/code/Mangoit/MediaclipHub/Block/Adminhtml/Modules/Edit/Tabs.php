<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml\Modules\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        
        parent::_construct();
        $this->setId('checkmodule_modules_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Modules Information'));
    }
}
