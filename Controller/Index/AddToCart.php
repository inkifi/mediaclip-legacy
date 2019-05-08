<?php
namespace Mangoit\MediaclipHub\Controller\Index;
// 2019-04-17
class AddToCart extends \Magento\Framework\App\Action\Action {
	/**
	 * 2019-04-17
	 * A request: `/mediacliphub/index/addToCart?projectId=c9264194-eace-45a1-b951-e849db3842a8`
	 */
	function execute() {
		$projectId = $this->getRequest()->getParam('projectId');
		$quote = df_cart()->getQuote();
		$addToCart = true;
		foreach ($quote->getAllVisibleItems() as $item) {
			if ($item->getMediaclipProjectId()) {
				if ($item->getMediaclipProjectId() == $projectId) {
				 //echo "<pre>";  print_r($item->getData());
					$quote_item = $this->_objectManager->create('\Magento\Quote\Model\Quote\Item\Option')->load($item->getItemId(),'item_id');

					$product_id = $quote_item->getProductId();

					$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($product_id);

					$mediaClipProject = $this->getMediaClipProject($projectId);

					$obj = json_decode($mediaClipProject->getProjectDetails(), true);

					$quantity = 1;

					$checkToAppendQty = $this->checkToAppendQty($product);
					if ($checkToAppendQty) {
						$quantity = $this->getProductQuantity($obj['items']);
					}

					$price = $this->getProductCalculatedPrice($product_id, $obj, 1);


					$productOptions = $product->getOptions();
					$product_option = array();
					if ($productOptions) {
						foreach($productOptions as $optionId => $optionDetail){

							if ($optionDetail->getDefaultTitle() == 'Additional') {
								$additional = $this->getAdditionalSheetsPhotobook($mediaClipProject, $product_id);
								$product_option[$optionId] = $additional;
								$product_option['option_'.$optionId] = $additional;
							}
						}
					}

					$itemOptions = $item->getOptions();

					foreach ($itemOptions as $option) {
						//print_r($option->getData());
						if($option->getCode() == 'info_buyRequest')
						{
							$optionVal = $option->getValue();
							$unserialized = json_decode($optionVal,true);
							//$unserialized = unserialize($optionVal);
							//print_r($unserialized);
							if (isset($unserialized['options'])) {
								foreach ($unserialized['options'] as $key => $value) {
									if (isset($product_option[$key])) {
										$unserialized['options'][$key]= $product_option[$key];
										$option->setValue(json_encode($unserialized));
									}
								}
							}
						}elseif (isset($product_option[$option->getCode()])) {
							$option->setValue($product_option[$option->getCode()]);
						}
					}
					$storeId = df_store_id(); /** @var int $storeId */
					$item->setStoreId($storeId);
					$item->setOptions($itemOptions);
					$item->setCustomPrice($price);
					$item->setOriginalCustomPrice($price);
					$item->getProduct()->setIsSuperMode(true);
					$item->setQty($quantity);
					$item->setProduct($product);
					$item->save();
					$quote->collectTotals()->save();

					$this->messageManager->addSuccess(__('Add to cart successfully.'));
					//$session->addSuccess($message);

					$addToCart = false;
				}
			}
		}

		if ($addToCart) {

			$mediaClipProject = $this->getMediaClipProject($projectId);



			$product_id = $mediaClipProject->getStoreProductId();

			$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($product_id);

			//$formKey = $session->getFormKey();
			$formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey();
			$obj = df_json_decode($mediaClipProject->getProjectDetails());
			df_assert($obj);
			$quantity = 1;
			$checkToAppendQty = $this->checkToAppendQty($product);
			if ($checkToAppendQty) {
				$quantity = $this->getProductQuantity($obj['items']);
			}
			$price = $this->getProductCalculatedPrice($product_id, $obj, 1);

			$options = $product->getOptions();
			$option = array();
			if ($options) {
				foreach($options as $optionDetail){


					$optionTitle = $optionDetail->getTitle();
					if ($optionTitle == 'Additional') {
						$optionArray[$optionDetail->getData('option_id')] = $this->getAdditionalSheetsPhotobook($mediaClipProject, $product_id);
					} else if($optionTitle == 'Project') {
						$optionArray[$optionDetail->getData('option_id')] = $projectId;
					} else {
						$project_details = $mediaClipProject->getProjectDetails();
						if ($project_details) {
							$project_details = json_decode($project_details, true);
							$optionsDetails = json_decode($project_details['properties']['option_details'], true);
						}
						$optionArray[$optionDetail->getData('option_id')] = $optionsDetails[$optionDetail->getData('option_id')];
					}
				}
				$option['options'] = $optionArray;
			}
			$params = array(
				'product' => $product_id,
				'form_key' => $formKey,
				'qty' => $quantity,
				'price' => $price,
			);

			if (!empty($option)) {
				$params = array_merge($params, $option);
			}
			try{

					$product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
					df_cart()->addProduct($product, $params)->save();
					 $this->messageManager->addSuccess(__('Add to cart successfully.'));

				foreach ($quote->getAllVisibleItems() as $item) {

					if ($item->getMediaclipProjectId()) {
						if ($item->getMediaclipProjectId() == $projectId) {

							$quote_item = $this->_objectManager->create('\Magento\Quote\Model\Quote\Item\Option')->load($item->getId());
							//echo "<pre>"; print_r($item->getData()); die();
							$product_id = $quote_item->getProductId();

							$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($product_id);

							$item->setCustomPrice($price);
							$item->setOriginalCustomPrice($price);
							$item->getProduct()->setIsSuperMode(true);
							//$item->setProduct($product);
							$item->save();
						}
					}

				}
			   //echo "<pre>"; print_r(get_class_methods($quote));die();
				//$session->addSuccess($message);
			}
			catch (\Magento\Framework\Exception\LocalizedException $e) {
				 $this->messageManager->addException(
					 $e,
					 __('%1', $e->getMessage())
				 );
			}
			catch (\Exception $e) {}
			$quote->collectTotals()->save();
		}
		// 2019-05-08 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Fix the Mediaclip edit cart scenario for the US store»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/12
		$this->getResponse()->setRedirect(df_url('checkout/cart/index'));
	}

	function getMediaClipProject($projectId){
		return $this->_objectManager->get('\Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id');
	}

	function checkToAppendQty($product){

		$response = true;
		if ($product->getMediaclipMinimumPrintsAllow() && $product->getMediaclipMinimumPrintsCount() != '' && $product->getMediaclipExtraPrintsPrice() != '') {
			return false;
		}
		return $response;
	}

	function getProductQuantity($items){
		$quantity = 0;
		if (!empty($items)) {
			foreach ($items as $item) {
				$quantity = $quantity + $item['quantity'];
			}
		}
		return $quantity;
	}

	function getProductCalculatedPrice($product_id, $mediaclip_obj, $quantity){

		$price = 0;

		$product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
		$product = $product->load($product_id);

		if ($product) {
			$product_price = $product->getPrice();
			$custom_option_price = 0;
			$additionalPrice = 0;

			if (isset($mediaclip_obj['properties']['option_details']) && !empty($mediaclip_obj['properties']['option_details'])) {
				$option_details = json_decode($mediaclip_obj['properties']['option_details']);

				$product_options = $product->getOptions();

				foreach ($option_details as $oid => $ovalue) {
					if ($product_options) {

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
		$session = $this->_objectManager->get('Magento\Customer\Model\Session');
		$session->setCustomPriceObserver($price);
		return $price;
	}

	function getProductAdditionalPriceAmount($productId){
		$product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
		if ($product->getMediaClipExtrasheetamt() && is_numeric($product->getMediaClipExtrasheetamt())) {
			$additionalAmount = $product->getMediaClipExtrasheetamt();
			return $additionalAmount;
		}
		return 0;
	}
}