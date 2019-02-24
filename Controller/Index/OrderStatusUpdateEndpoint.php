<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Inkifi\Mediaclip\Event;
use Inkifi\Mediaclip\H\Logger as L;
// 2018-11-02
class OrderStatusUpdateEndpoint extends \Magento\Framework\App\Action\Action {
	/** 2018-11-02 */
    function execute() {
        $e = Event::s(); /** @var Event $ev */
        if ($s = $e['status/value']) {  /** @var string|null $s */
			L::l("Status: $s");
			L::l($e->j());
            if ('AvailableForDownload' === $s) {
				\Inkifi\Mediaclip\H\AvailableForDownload::p();
            }
            else if ('Shipped' === $s) {
				\Inkifi\Mediaclip\H\Shipped::p();
            }
        }
    }
}