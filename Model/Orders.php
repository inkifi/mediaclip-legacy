<?php
namespace Mangoit\MediaclipHub\Model;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders as R;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders\Collection as C;
/**
 * 2019-03-13
 * A `mediaclip_orders` row is associated with an unique `magento_order_id`,
 * the following query returns an empty result set:
 *	SELECT * FROM
 *		mediaclip_orders mo1
 *	INNER JOIN
 *		mediaclip_orders mo2
 *	ON
 *			mo1.id <> mo2.id
 *		AND
 *			mo1.magento_order_id = mo2.magento_order_id
 *	ORDER BY mo1.created_at DESC;
 */
class Orders extends \Magento\Framework\Model\AbstractModel {
	/** 2019-03-13 */
	function _construct() {$this->_init(R::class);}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload::_p()
	 */
	function markAsDownloaded() {
		$this->setData(self::F__DOWNLOAD_STATUS, 1);
		$this->save();
	}

	/**
	 * 2019-03-13
	 * @used-by \Inkifi\Mediaclip\Event::mo()
	 * @param string $oidE		«58312» or «staging-58312»
	 * @return self
	 */
	static function byOIdE($oidE) {
		$c = C::i();
		$c->addFieldToFilter('magento_order_id', ['eq' => $oidE]);
		df_assert_eq(1, $c->count());
		return $c->getFirstItem();
	}

	/**
	 * 2019-03-13
	 * @used-by markAsDownloaded()
	 * @used-by \Mangoit\MediaclipHub\Setup\UpgradeSchema::upgrade()
	 */
	const F__DOWNLOAD_STATUS = 'order_download_status';
}