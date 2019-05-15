<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Df\Framework\W\Result\Json;
use Inkifi\Mediaclip\Price;
use Magento\Catalog\Model\Product as P;
/**
 * 2019-05-15
 * «Make the Mediaclip's «Get Price» endpoint compatible with the Magento 2 multistore mode»
 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
 * https://doc.mediaclip.ca/hub/store-endpoints/get-price
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class GetPriceEndpoint extends \Df\Framework\Action {
	/**
	 * 2019-05-15
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {
		/** @var array(string => mixed) $r */
		try {
			/**
			 * 2019-05-15
			 * https://doc.mediaclip.ca/hub/store-endpoints/get-price
			 * A request:
			 * {
			 *		"storeData": {
			 *			"userId": "70994"
			 *		},
			 *		"projectId": "bcd16e0f-241f-4294-a8a0-094b709ac53e",
			 *		"properties": {
			 *			"storeProductId": "80321"
			 *		},
			 *		"items": [
			 *			{
			 *				"productId": "$(package:inkifi/us-prints)/products/mini-prints",
			 *				"plu": "US-INKIFI-MP",
			 *				"quantity": 1,
			 *				"properties": {
			 *					"storeProductId": "80321"
			 *				}
			 *			}
			 *		]
			 *	}
			 */
			$req = df_json_decode(file_get_contents('php://input')); /** @var array(string => mixed) $req */
			df_sentry_extra($this, 'Request', $req);
			$pid = (int)$req['properties']['storeProductId']; /** @var int $pid */
			$quantity = 1;
			/**
			 * 2019-05-15
			 * 1) «Make the Mediaclip's «Get Price» endpoint compatible with the Magento 2 multistore mode»
			 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/issues/13
			 * 2) https://magento.stackexchange.com/a/177164
			 */
			$p = df_product($pid, true); /** @var P $p */
			$checkToAppendQty = $this->checkToAppendQty($p);
			if ($checkToAppendQty) {
				$quantity = $this->getProductQuantity($req['items']);
			}
			$price = Price::get($p, $req, $quantity);
			$r = ['price' => ['original' => $this->getPriceHtml($price)]];
		}
		catch (\Exception $e) {
			df_response_code(500);
			$r = ['error' => df_ets($e)];
			df_log($e, $this);
			if (df_my_local()) {
				throw $e; // 2016-03-27 It is convenient for me to the the exception on the screen.
			}
		}
		return Json::i($r);
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

	function getPriceHtml($productPrice){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
		$formattedPrice = $priceHelper->currency($productPrice, true, false);
	 	return $formattedPrice;
	}
}