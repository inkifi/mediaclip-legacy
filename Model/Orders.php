<?php
namespace Mangoit\MediaclipHub\Model;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders as R;
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
}