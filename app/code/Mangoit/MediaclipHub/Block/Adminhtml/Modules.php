<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml;

class Modules extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_modules';/*block grid.php directory*/
        $this->_blockGroup = 'Mangoit_MediaclipHub';
        $this->_headerText = __('Modules');
        $this->_addButtonLabel = __('Add Module');
        parent::_construct();
    }
}
