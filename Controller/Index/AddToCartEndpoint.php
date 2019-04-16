<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Mangoit\MediaclipHub\Model\Mediaclip as M;
// 2019-04-17
class AddToCartEndpoint extends \Magento\Framework\App\Action\Action {
	/**
	 * 2019-04-17
	 * A request:
	 *	{
	 *		"storeData": {
	 *			"userId": "58147"
	 *		},
	 *		"projectId": "ee970c91-16d6-490a-9a71-1e205cfb4859",
	 *		"properties": {
	 *			"option_details": "{\"20530\":\"44379\",\"20639\":\"44847\"}",
	 *			"storeProductId": "74134"
	 *		},
	 *		"items": [
	 *			{
	 *				"productId": "$(package:inkifi/prints)/products/framed-print-12x12-black",
	 *				"plu": "INKIFI-IGF-12-BL",
	 *				"quantity": 1,
	 *				"properties": {
	 *					"option_details": "{\"20530\":\"44379\",\"20639\":\"44847\"}",
	 *					"storeProductId": "74134"
	 *				}
	 *			}
	 *		]
	 *	}
	 */
	function execute() {
		$req = json_decode(file_get_contents('php://input'), true); /** @var array(string => mixed) $req */
		$m = df_new_om(M::class); /** @var M $m */
		$m->load($req['projectId'], 'project_id');
		$m->setProjectDetails(json_encode($req));
		$m->save();
	}
}