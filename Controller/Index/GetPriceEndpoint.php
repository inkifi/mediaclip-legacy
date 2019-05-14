<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class GetPriceEndpoint extends Action {
	protected $logger;
	/**
	 * @param Context     $context
	 * @param PageFactory $resultPageFactory
	 */
	function __construct(Context $context, \Psr\Log\LoggerInterface $logger, PageFactory $resultPageFactory) {
		$this->logger = $logger;
		parent::__construct($context);
 
	}

	/** 2019-05-14 */
	function execute() {
		$json = file_get_contents('php://input');
		$obj = json_decode($json, true);
		$product_id = $obj['properties']['storeProductId'];
		$quantity = 1;
		$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($product_id);
		$checkToAppendQty = $this->checkToAppendQty($product);
		if ($checkToAppendQty) {
			$quantity = $this->getProductQuantity($obj['items']);
		}
		$price = $this->getProductCalculatedPrice($product_id, $obj, $quantity);
		$price = $this->getPriceHtml($price);
		$priceData = array("price" => array("original" => $price));
		echo json_encode($priceData);
		exit;
	}

	/**
	 * 2019-05-14
	 * @used-by execute()
	 * @param $product
	 * @return bool
	 */
	private function checkToAppendQty($product){
		$response = true;
		if ($product->getMediaclipMinimumPrintsAllow() && $product->getMediaclipMinimumPrintsCount() != '' && $product->getMediaclipExtraPrintsPrice() != '') {
			return false;
		}
		return $response;
	}

	/**
	 * 2019-05-14
	 * @used-by execute()
	 * @param $items
	 * @return int
	 */
	private function getProductQuantity($items){
		$quantity = 0;
		if (!empty($items)) {
			foreach ($items as $item) {
				$quantity = $quantity + $item['quantity'];
			}
		}
		return $quantity;
	}

	/**
	 * 2019-05-14
	 * @used-by execute()
	 * @param $product_id
	 * @param $mediaclip_obj
	 * @param $quantity
	 * @return float|int
	 */
	private function getProductCalculatedPrice($product_id, $mediaclip_obj, $quantity){
		$price = 0;
		$product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
		$product->load($product_id);
		if ($product) {
			$product_price = $product->getPrice();
			$custom_option_price = 0;
			$additionalPrice = 0;
			if (isset($mediaclip_obj['properties']['option_details']) && !empty($mediaclip_obj['properties']['option_details'])) {
				$option_details = json_decode($mediaclip_obj['properties']['option_details']);
				$product_options = $product->getOptions();
				foreach ($option_details as $oid => $ovalue) {
					foreach ($product_options as $pvalue) {
						 //print_r($pvalue->getData('option_id'));
					 //print_r($pvalue);
						if ($oid == $pvalue->getData('option_id')) {
							$optionValues = $pvalue->getValues();
							if ($optionValues) {

								foreach ($optionValues as $_value) {
									if ($ovalue == $_value->getData('option_type_id')) {
										$custom_option_price = $custom_option_price + $_value->getPrice();
									}
								}
							}
						}
					}
				}
			}
			foreach ($mediaclip_obj['items'] as $key => $value) {
				if (isset($value['photobookData']) && isset($value['photobookData']['additionalSheetCount'])) {
					if ($value['photobookData']['additionalSheetCount'] > 0) {
						$additionalPriceAmount = $this->getProductAdditionalPriceAmount($product_id);
						$additional = $value['photobookData']['additionalSheetCount'];
						$additionalPrice = $additionalPrice + ($additionalPriceAmount * (int)$additional);
					}
				}
			}
			if ($product->getMediaclipMinimumPrintsAllow()) {
				$min_allow = $product->getMediaclipMinimumPrintsCount();
				$add_price = $product->getMediaclipExtraPrintsPrice();
				if ($min_allow != '' && is_numeric($min_allow) && $add_price != '' && is_numeric($add_price)) {
					foreach ($mediaclip_obj['items'] as $mediaclipItem) {
						if ($mediaclipItem['properties']['storeProductId'] == $product_id) {
							$designer_prints = $mediaclipItem['quantity'];
							if ($designer_prints > $min_allow) {
								$diff = $designer_prints - $min_allow;
								$additionalPrice = $diff*$add_price;
							} else if ($designer_prints < $min_allow) {
								$diff = $min_allow - $designer_prints;
								$session = $this->_objectManager->get('Magento\Customer\Model\Session');
								$session->setCanAddMoreMediaclipPrintsPrompt(1);
								$add_more_prompt['diff'] = $diff;
								$add_more_prompt['project_id'] = $mediaclip_obj['projectId'];
								$add_more_prompt['product_id'] = $product_id;
								$session->setAddMoreMediaclipPrints($add_more_prompt);
							}
						}
					}
				}
			}
			$price = $product_price + $custom_option_price + $additionalPrice;
			$price = $price*$quantity;
		}
		return $price;
	}

	/**
	 * 2019-05-14
	 * @used-by getProductCalculatedPrice()
	 * @param $productId
	 * @return int
	 */
	private function getProductAdditionalPriceAmount($productId){
		$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
		if ($product->getMediaClipExtrasheetamt() && is_numeric($product->getMediaClipExtrasheetamt())) {
			$additionalAmount = $product->getMediaClipExtrasheetamt();
			return $additionalAmount;
		}
		return 0;
	}

	function getPriceHtml($productPrice){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
		$formattedPrice = $priceHelper->currency($productPrice, true, false);
	 	return $formattedPrice;
	}
}