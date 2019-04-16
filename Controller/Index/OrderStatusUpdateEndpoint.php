<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Inkifi\Mediaclip\Event;
use Inkifi\Mediaclip\H\Logger as L;
/**
 * 2018-11-02
 * 2019-04-17
 * 1) A request:
 *	{
 *		"id": "f533aae3-c9ea-49e4-8ada-1fafacdcbfe3",
 *		"order": {
 *			"storeData": {
 *				"orderId": "staging-63926"
 *			}
 *		},
 *		"storeData": {
 *			"lineNumber": 1,
 *			"productId": "74134",
 *			"properties": {
 *				"option_details": "{\"20530\":\"44379\",\"20639\":\"44847\"}",
 *				"storeProductId": "74134"
 *			}
 *		},
 *		"projectId": "44000fd3-389e-44e0-aaa4-66627c3752df",
 *		"status": {
 *			"value": "AvailableForDownload",
 *			"effectiveDateUtc": "2019-04-16T19:22:56.1237975Z"
 *		}
 *	}
 * 2) https://doc.mediacliphub.com/hub/store-endpoints/status-update
 */
class OrderStatusUpdateEndpoint extends \Magento\Framework\App\Action\Action {
	/** 2018-11-02 */
    function execute() {
    	try {
			$e = Event::s(); /** @var Event $ev */
			$s = $e['status/value'];
			L::l("Status: $s");
			L::l($e->j());
			if ('AvailableForDownload' === $s) {
				\Inkifi\Mediaclip\H\AvailableForDownload::p();
			}
		}
		catch (\Exception $e) {
			df_log_e($e);
			df_response_code(500);
		}
    }
}