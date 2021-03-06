<?php
namespace Mangoit\MediaclipHub\Block\Adminhtml\Product\Edit\Tab;
use Mangoit\MediaclipHub\Model\Product as mProduct;
class ProductInformation
	extends \Magento\Backend\Block\Widget\Form\Generic
	implements \Magento\Backend\Block\Widget\Tab\TabInterface {
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
	function __construct(
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
		$themeValues = $helper->getThemes();
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
				'values' => $helper->getModules(),
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
			mProduct::F__LABEL,
			'text',
			[
				'name' => mProduct::F__LABEL,
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
			mProduct::F__PLU,
			'text',
			[
				'name' => mProduct::F__PLU,
				'label' => __('PLU'),
				'title' => __('PLU'),
				'required' => true,
			]
		);
		$fieldset->addField(
			mProduct::F__FRAME_COLOUR,
			'text',
			[
				'name' => mProduct::F__FRAME_COLOUR,
				'label' => __('Frame Colour'),
				'title' => __('Frame Colour'),
				'required' => false,
			]
		);
		$fieldset->addField(
			mProduct::F__PWINTY_PRODUCT_SKU,
			'text',
			[
				'name' => mProduct::F__PWINTY_PRODUCT_SKU,
				'label' => __('Pwinty Product Name'),
				'title' => __('Pwinty Product Name'),
				'required' => false,
			]
		);
		/**
		 * 2019-05-01
		 * 1) «Implement the `preferredShippingMethod` backend input and pass its value to Pwinty»:
		 * https://github.com/inkifi/pwinty/issues/1
		 * 2) API 3.0: «Possible values are `Budget`, `Standard`, `Express`, and `Overnight`»:
		 * https://www.pwinty.com/api#orders-create
		 */
		$fieldset->addField(mProduct::F__PWINTY_SHIPPING_METHOD, 'select', [
		   'label' => __('[Pwinty] Shipping Method')
		   ,'name' => mProduct::F__PWINTY_SHIPPING_METHOD
		   ,'options' => dfa_combine_self(['Standard', 'Budget', 'Express', 'Overnight'])
		   ,'title' => __('[Pwinty] Shipping Method')
		]);
		$fieldset->addField(
			'dust_jacket_popup',
			'select',
			[
				'name' => 'dust_jacket_popup',
				'label' => __('Dust Jacket Popup'),
				'title' => __('Dust Jacket Popup'),
				'required' => false,
				'values' => $helper->getDustjacketpopup(),
			]
		);
		$fieldset->addField(
		   mProduct::F__FTP_JSON,
		   'select',
			[
			   'name' => mProduct::F__FTP_JSON,
			   'label' => __('Send Json'),
			   'title' => __('Send Json'),
			   'options' => ['0' => __('No'),'1' => __('Yes') ],
			]
		);

		$fieldset->addField(
		   mProduct::F__INCLUDE_QUANTITY_IN_JSON,
		   'select',
			[
			   'name' => mProduct::F__INCLUDE_QUANTITY_IN_JSON,
			   'label' => __('Include Quantity In JSON'),
			   'title' => __('Include Quantity In JSON'),
			   'options' => ['0' => __('No'),'1' => __('Yes') ],
			]
		);
		$fieldset->addField(
			mProduct::F__JSON_CODE,
			'text',
			[
				'name' => mProduct::F__JSON_CODE,
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
	function getTabLabel()
	{
		return __('Product Information');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	function getTabTitle()
	{
		return __('Product Information');
	}

	/**
	 * {@inheritdoc}
	 */
	function canShowTab()
	{
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	function isHidden()
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
