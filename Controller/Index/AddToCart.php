<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Inkifi\Mediaclip\Price;
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Quote\Model\Quote\Item\Option as QIO;
// 2019-04-17
class AddToCart extends \Magento\Framework\App\Action\Action {
	/**
	 * 2019-04-17
	 * A request: `/mediacliphub/index/addToCart?projectId=c9264194-eace-45a1-b951-e849db3842a8`
	 */
	function execute() {
		try {
			/** @var string|null $projectId */
			if (!($projectId = $this->getRequest()->getParam('projectId')))  {
				// 2019-05-29 https://log.mage2.pro/inkifi/mangoit/issues/302
				df_error('Invalid request: the `projectId` parameter is absent.');
			}
			$quote = df_cart()->getQuote();
			$addToCart = true;
			foreach ($quote->getAllVisibleItems() as $qi) { /** @var QI $qi */
				if ($qi->getMediaclipProjectId()) {
					if ($qi->getMediaclipProjectId() == $projectId) {
						/** @var QIO $qio */
						$qio = df_new_om(QIO::class)->load($qi->getItemId(),'item_id');
						$pid = (int)$qio->getProductId(); /** @var int $pid */
						/**
						 * 2019-05-15
						 * 1) «Make the Mediaclip's «Get Price» endpoint compatible
						 * with the Magento 2 multistore mode»:
						 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
						 * 2) https://magento.stackexchange.com/a/177164
						 */
						$product = df_product($pid, true); /** @var P $product */
						$mediaClipProject = $this->getMediaClipProject($projectId);
						$obj = json_decode($mediaClipProject->getProjectDetails(), true);
						$quantity = 1;
						$checkToAppendQty = $this->checkToAppendQty($product);
						if ($checkToAppendQty) {
							$quantity = $this->getProductQuantity($obj['items']);
						}
						$price = Price::get($product, $obj, 1, true);
						$productOptions = $product->getOptions();
						$product_option = array();
						if ($productOptions) {
							foreach($productOptions as $optionId => $optionDetail){

								if ($optionDetail->getDefaultTitle() == 'Additional') {
									$additional = $this->getAdditionalSheetsPhotobook($mediaClipProject, $pid);
									$product_option[$optionId] = $additional;
									$product_option['option_'.$optionId] = $additional;
								}
							}
						}
						$itemOptions = $qi->getOptions();
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
						$qi->setStoreId($storeId);
						$qi->setOptions($itemOptions);
						$qi->setCustomPrice($price);
						$qi->setOriginalCustomPrice($price);
						$qi->getProduct()->setIsSuperMode(true);
						$qi->setQty($quantity);
						$qi->setProduct($product);
						$qi->save();
						$quote->collectTotals()->save();

						$this->messageManager->addSuccess(__('Add to cart successfully.'));
						//$session->addSuccess($message);

						$addToCart = false;
					}
				}
			}
			if ($addToCart) {
				$mediaClipProject = $this->getMediaClipProject($projectId);
				if (!($pid = $mediaClipProject->getStoreProductId())) {
					// 2019-05-29 https://log.mage2.pro/inkifi/mangoit/issues/302
					df_error('The project is not linked to a product: ' . $projectId);
				}
				/**
				 * 2019-05-15
				 * 1) «Make the Mediaclip's «Get Price» endpoint compatible
				 * with the Magento 2 multistore mode»:
				 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
				 * 2) https://magento.stackexchange.com/a/177164
				 */
				$product = df_product($pid, true); /** @var P $product */
				//$formKey = $session->getFormKey();
				$formKey = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey();
				$obj = df_json_decode($mediaClipProject->getProjectDetails());
				df_assert($obj);
				$quantity = 1;
				$checkToAppendQty = $this->checkToAppendQty($product);
				if ($checkToAppendQty) {
					$quantity = $this->getProductQuantity($obj['items']);
				}
				$price = Price::get($product, $obj, 1, true);

				$options = $product->getOptions();
				$option = array();
				if ($options) {
					foreach($options as $optionDetail){
						$optionTitle = $optionDetail->getTitle();
						if ($optionTitle == 'Additional') {
							$optionArray[$optionDetail->getData('option_id')] = $this->getAdditionalSheetsPhotobook($mediaClipProject, $pid);
						} else if($optionTitle == 'Project') {
							$optionArray[$optionDetail->getData('option_id')] = $projectId;
						} else {
							$project_details = $mediaClipProject->getProjectDetails();
							if ($project_details) {
								$project_details = json_decode($project_details, true);
								$optionsDetails = json_decode(
									$project_details['properties']['option_details'], true
								);
							}
							$optionArray[$optionDetail->getData('option_id')] = $optionsDetails[$optionDetail->getData('option_id')];
						}
					}
					$option['options'] = $optionArray;
				}
				$params = array(
					'product' => $pid,
					'form_key' => $formKey,
					'qty' => $quantity,
					'price' => $price,
				);
				if (!empty($option)) {
					$params = array_merge($params, $option);
				}
				try {
					/**
					 * 2019-05-15
					 * 1) «Make the Mediaclip's «Get Price» endpoint compatible
					 * with the Magento 2 multistore mode»:
					 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
					 * 2) https://magento.stackexchange.com/a/177164
					 */
					$product = df_product($pid, true); /** @var P $product */
					df_cart()->addProduct($product, $params)->save();
					$this->messageManager->addSuccess(__('Add to cart successfully.'));
					foreach ($quote->getAllVisibleItems() as $qi) { /** @var QI $qi */
						if ($qi->getMediaclipProjectId()) {
							if ($qi->getMediaclipProjectId() == $projectId) {
								$qi->setCustomPrice($price);
								$qi->setOriginalCustomPrice($price);
								$qi->getProduct()->setIsSuperMode(true);
								$qi->save();
							}
						}
					}
				}
				catch (\Magento\Framework\Exception\LocalizedException $e) {
					 $this->messageManager->addException($e, __('%1', $e->getMessage()));
					 df_log($e, $this);
				}
				catch (\Exception $e) {df_log($e, $this);}
				$quote->collectTotals()->save();
			}
		}
		catch (\Exception $e) {df_log($e, $this);}
		// 2019-05-08 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Fix the Mediaclip edit cart scenario for the US store»
		// https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/12
		$this->getResponse()->setRedirect(df_url('checkout/cart/index'));
	}

	/**
	 * 2019-05-15
	 * @used-by execute()
	 * @param $projectId
	 * @return mixed
	 */
	private function getMediaClipProject($projectId){
		return $this->_objectManager->get('\Mangoit\MediaclipHub\Model\Mediaclip')->load($projectId, 'project_id');
	}


	/**
	 * 2019-05-15
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
	 * 2019-05-15
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
}