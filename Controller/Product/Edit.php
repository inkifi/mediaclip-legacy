<?php
namespace Mangoit\MediaclipHub\Controller\Product;
use Magento\Catalog\Model\Product as P;
use Mangoit\MediaclipHub\Model\Product as mProduct;
class Edit extends \Magento\Framework\App\Action\Action {

	protected $resultPageFactory;
/**
	 * @var \Magento\Framework\App\Response\Http
	 */
	protected $response;
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Action\Context  $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\UrlInterface $response,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->response = $response;
		parent::__construct($context);
	}

	/**
	 * Execute view action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	function execute() {
		$refererUrl = $this->_redirect->getRefererUrl();
		$productId  = (int) $this->getRequest()->getParam('product');
		if (!$productId) {
			$this->getResponse()->setRedirect($refererUrl);
			return $this;
		}
		$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
		if (!$product->getId()) {
			$this->getResponse()->setRedirect($refererUrl);
			return $this;
		}
		$session = $this->_objectManager->get('Magento\Customer\Model\Session');
		$session->setProduct($product);
		$mode = $this->getRequest()->getParam('mode');
		if (!$mode) {
			$mode = 'newProject';
			// load mediaclip module
			$attributeSet = $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
			$attributeSetRepository = $attributeSet->get($product->getAttributeSetId());
			$mediaclip_module = $attributeSetRepository->getAttributeSetName();
			if (!$mediaclip_module) {
				$this->getResponse()->setRedirect($refererUrl);
				return $this;
			}
			$mediaclip_module = ucfirst(strtolower($mediaclip_module));
			$mediaclip_product = $this->mProduct($product, $mediaclip_module);
			if (!$mediaclip_product) {
				$this->getResponse()->setRedirect($refererUrl);
				return $this;
			}
			$session->setMediaclipProduct($mediaclip_product);
			$mediaclip_theme_url = $mediaclip_product['product_theme'];
			if (!$mediaclip_theme_url) {
				$mediaclip_theme_url = NULL;
			}
			$mediaclip_product_id = $mediaclip_product['product_id'];
			$additionalProprties = array();
			$storeProductId = $product->getId();
			$product_options = $this->getStepData($storeProductId);
			if (isset($product_options['options'])) {
				$additionalProprties['option_details'] = json_encode($product_options['options']);
			}
			$helper = mc_h();
			$storeUserId = $helper->getCustomerId();
			$userToken = $helper->HandleUserToken($storeUserId, $session->getMediaClipToken());
			ikf_logger('request_token_productedit')->info(json_encode(["Request Token : " => $userToken]));
			$session->setMediaClipToken($userToken);
			$mediaclip_product_date_options = $this->getMediaClipProductDateOptions($product);
			$projectId = $helper->CreateProject(
				$mediaclip_module, $mediaclip_product_id, $mediaclip_theme_url, $storeProductId
				,$userToken->token, $additionalProprties, $mediaclip_product_date_options
			);
			$session->setMediaClipProjectId($projectId);
			$mediaClipData['project_id'] = $projectId;
			$mediaClipData['user_id'] = $storeUserId;
			$mediaClipData['store_product_id'] = $storeProductId;
			$mediaClipData['project_details'] = '';
			$model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')->setData($mediaClipData);
			$model->save();
		}
		else{
			$projectId = $this->getRequest()->getParam('projectId');
			$session = $this->_objectManager->get('Magento\Customer\Model\Session');
			$session->setMediaClipProjectId($projectId);
			$model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Mediaclip')
				->load($projectId, 'project_id')
			;
			$project_details = $model->getProjectDetails();
			if (isset($project_details['properties']['option_details'])) {
				if ($project_details) {
					$project_details = json_decode($project_details, true);
					$optionsDetails = json_decode($project_details['properties']['option_details'], true);
				}
				$options = $product->load($productId)->getOptions();
				if ($optionsDetails) {
					$productUrl = $product->load($productId)->getProductUrl();
					$productOptionsId = array();
					foreach ($options as $key => $value) {
						$optionTitle = $value->getTitle();
						if ($optionTitle == 'Project' || $optionTitle == 'Additional' ) {
							continue;
						}
						$productOptionsId[] = $key ;
					}
					$insectedArray = array_intersect_key(array_flip($productOptionsId),$optionsDetails);
					if (count($insectedArray) != count($productOptionsId)) {
						$customizeProjectUrl = $productUrl ;
						$resultRedirect = $this->resultRedirectFactory->create();
						$resultRedirect->setPath($customizeProjectUrl);
						return $resultRedirect;
					}
				}
			}
		}
		$storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$store = $storeManager->getStore();
		$customizeProjectUrl = $store->getBaseUrl().'mediacliphub/index/customizeproject/'."projectId/".$projectId."/mode/".$mode;
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setPath($customizeProjectUrl);
		return $resultRedirect;
	}
	function getMediaClipModule($_product){

		$option_id = $_product->getMediaclipModule();
		$mediaclipModule = NULL;
		if ($option_id  > 0) {
			$attr = 'mediaclip_module';
			$attr = $_product->getResource()->getAttribute($attr);
			if ($attr->usesSource()) {
				$mediaclipModule = $attr->getSource()->getOptionText($option_id);
			}
		}
		return $mediaclipModule;
	}
	function getModuleID($module_name){
		$model = $this->_objectManager->create('Mangoit\MediaclipHub\Model\Modules')->load($module_name, 'module_name');
		$module_id = $model->getData('id');

		return $module_id;
	}

	/**
	 * 2019-04-11
	 * @used-by mProduct()
	 * @param $stepData
	 * @param $productOptions
	 * @param $_module
	 * @return bool
	 */
	function getMediaClipProductSku($stepData, $productOptions, $_module){
		foreach ($stepData['options'] as $oid => $ovalue) {
			if ($productOptions) {
				foreach($productOptions as $optionId => $optionDetail){
					if ($oid == $optionDetail->getOptionId()) {
						$optionValues = $optionDetail->getValues();
						if (is_array($optionValues)) {
							foreach ($optionValues as $vid => $_value) {
								if ($ovalue == $_value->getOptionTypeId()) {
									if ($_value->getSku()) {
										$model = $this->_objectManager->create(mProduct::class);
										$_module = $this->getModuleID($_module);
										return mProduct::bySku($_value->getSku(), $_module) ?: false;
									}
								}
							}
						}
					}
				}
			}
		}
		return false;
	}

	function getMediaClipProductDateOptions($_product){
		$productId = $_product->getId();
		$stepData = $this->getStepData($productId);
		$mediaClipProductDateOptions = false;
		if ($stepData) {
			if (isset($stepData['options'])) {
				$productOptions = $_product->getOptions();
				foreach ($stepData['options'] as $oid => $ovalue) {
					if ($productOptions) {
						foreach($productOptions as $optionId => $optionDetail){
							if ($oid == $optionDetail->getOptionId()) {
								if ($optionDetail->getType() == 'date') {
									$ovalueArray = (array)$ovalue;
									if (isset($ovalueArray['month']) && isset($ovalueArray['year'])) {
										$mediaClipProductDateOptions['startYear'] = $ovalueArray['year'];
										$mediaClipProductDateOptions['startMonth'] = $ovalueArray['month'];
									}
								}
							}
						}
					}
				}
			}
		}
		return $mediaClipProductDateOptions;
	}

	/**
	 * @used-by execute()
	 * @param P $p
	 * @param string $_module
	 * @return mProduct|null
	 */
	private function mProduct(P $p, $_module) {
		$r = null;
		if ($stepData = $this->getStepData($p->getId())) {
			if (!isset($stepData['options'])) {
				$r = mProduct::byProduct($p, $_module);
			}
			else {
				$r = $this->getMediaClipProductSku($stepData, $p->getOptions(), $_module);
				print_r($r);
				if (!$r) {
					$r = mProduct::byProduct($p, $_module);
				}
			}
		}
		return $r;
	}

	/**
	 * @used-by mProduct()
	 * @param $productId
	 * @return array|bool
	 */
	private function getStepData($productId){
		$stepData = $this->getRequest()->getParams();
		if (!empty($stepData)) {
			if (isset($stepData['options'])) {
				$stepData['options'] = json_decode($stepData['options']);
			}
			if (isset($stepData['product']) && $stepData['product'] == $productId) {
				return $stepData;
			}
		}
		return false;
	}
}