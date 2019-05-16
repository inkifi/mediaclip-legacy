<?php
namespace Mangoit\MediaclipHub\Model\ResourceModel\Orders;
use Mangoit\MediaclipHub\Model\Orders as M;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders as R;
// 2019-03-13
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/** 2019-03-13 */
	function _construct() {$this->_init(M::class, R::class);}

	/**
	 * 2019-03-13
	 * @used-by \Mangoit\MediaclipHub\Model\Orders::byOId()
	 * @return self
	 */
	static function i() {return df_new_om(__CLASS__);}
}