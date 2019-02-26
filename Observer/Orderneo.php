<?php
namespace Mangoit\MediaclipHub\Observer;
use Magento\Framework\Event\Observer as Ob;
class Orderneo implements \Magento\Framework\Event\ObserverInterface {
	/**
	 * 2019-02-26 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * The method's code was commented out:
	 * https://github.com/Inkifi-Connect/Media-Clip-Inkifi/blob/d7aad083/Observer/Orderneo.php#L28-L241
	 * @param Ob $observer
	 */
    function execute(Ob $observer) {ikf_logger('new_observer_check')->info('inObserver1');}
}