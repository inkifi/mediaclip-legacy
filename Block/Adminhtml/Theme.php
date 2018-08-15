<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml;

class Theme extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        
        $this->_controller = 'adminhtml_theme';/*block grid.php directory*/
        $this->_blockGroup = 'Mangoit_MediaclipHub';
        $this->_headerText = __('Mediaclip Theme');
        $this->_addButtonLabel = __('Add Theme');
        parent::_construct();
    }
}
