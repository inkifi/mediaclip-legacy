<?php
namespace Mangoit\MediaclipHub\Model\ResourceModel\Product;
use Mangoit\MediaclipHub\Model\Product as M;
use Mangoit\MediaclipHub\Model\ResourceModel\Product as R;
// 2019-03-13
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	/** 2019-03-13 */
	function _construct() {$this->_init(M::class, R::class);}
}