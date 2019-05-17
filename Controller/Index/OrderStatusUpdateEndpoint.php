<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Df\Framework\W\Result\Json;
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
class OrderStatusUpdateEndpoint extends \Df\Framework\Action {
	/**
	 * 2018-11-02
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @see \Mangoit\MediaclipHub\Controller\Index\OneflowResponse::execute()
	 * @return Json
	 */
    function execute() {
    	/** @var array(string => mixed) $r */
    	try {
			$e = Event::s(); /** @var Event $ev */
			$s = $e['status/value']; /** @var string $s */
			L::l("Status: $s");
			L::l($e->j());
			if (!df_my_local()) {
				df_sentry_extra($this, 'Event', $e->a());
				//df_sentry($this, "{$e->oidI()}: $s");
			}
			if ('AvailableForDownload' === $s) {
				\Inkifi\Mediaclip\H\AvailableForDownload::p();
			}
			$r = 'OK';
		}
		catch (\Exception $e) {
    		// 2019-05-17 https://doc.mediaclip.ca/hub/store-endpoints#replying-with-errors
    		df_response_code(500);
    		$r = ['code' => 500, 'message' => df_ets($e)];
			df_log($e, $this);
			if (df_my_local()) {
				throw $e; // 2016-03-27 It is convenient for me to the the exception on the screen.
			}
		}
		return Json::i($r);
    }
}