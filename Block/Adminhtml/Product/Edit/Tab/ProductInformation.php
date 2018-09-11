<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab;

class ProductInformation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('mediacliphub_product');
        $isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Details')]);
        $helper = mc_h();
        $moduleValues = $helper->getModules();
        $themeValues = $helper->getThemes();
        $dust_jacket_popup = $helper->getDustjacketpopup();
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
        $fieldset->addField(
            'module',
            'select',
            [
                'name' => 'module',
                'class' => 'validate-select',
                'label' => __('Mediaclip Module'),
                'title' => __('Mediaclip Module'),
                'required' => true,
                'values' => $moduleValues,
            ]
        );
        $fieldset->addField(
            'product_theme',
            'select',
            [
                'name' => 'product_theme',
                'label' => __('Mediaclip Product Theme'),
                'title' => __('Mediaclip Product Theme'),
                'values' => $themeValues,
            ]
        );
        $fieldset->addField(
            'product_label',
            'text',
            [
                'name' => 'product_label',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'product_id',
            'text',
            [
                'name' => 'product_id',
                'label' => __('Identifier'),
                'title' => __('Identifier'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'plu',
            'text',
            [
                'name' => 'plu',
                'label' => __('PLU'),
                'title' => __('PLU'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'frame_colour',
            'text',
            [
                'name' => 'frame_colour',
                'label' => __('Frame Colour'),
                'title' => __('Frame Colour'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'pwinty_product_name',
            'text',
            [
                'name' => 'pwinty_product_name',
                'label' => __('Pwinty Product Name'),
                'title' => __('Pwinty Product Name'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'dust_jacket_popup',
            'select',
            [
                'name' => 'dust_jacket_popup',
                'label' => __('Dust Jacket Popup'),
                'title' => __('Dust Jacket Popup'),
                'required' => false,
                'values' => $dust_jacket_popup,
            ]
        );
        $fieldset->addField(
           'ftp_json',
           'select',
            [
               'name' => 'ftp_json',
               'label' => __('Send Json'),
               'title' => __('Send Json'),
               'options' => ['0' => __('No'),'1' => __('Yes') ],
            ]
        );

        $fieldset->addField(
           'include_quantity_in_json',
           'select',
            [
               'name' => 'include_quantity_in_json',
               'label' => __('Include Quantity In JSON'),
               'title' => __('Include Quantity In JSON'),
               'options' => ['0' => __('No'),'1' => __('Yes') ],
            ]
        );

        
        
        $fieldset->addField(
            'json_code',
            'text',
            [
                'name' => 'json_code',
                'label' => __('JSON code'),
                'title' => __('JSON code'),
                'required' => false,
            ]
        );

        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Product Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Product Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
