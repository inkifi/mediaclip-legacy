<?php
namespace Mangoit\MediaclipHub\Model;
use Mangoit\MediaclipHub\Model\ResourceModel\Orders as R;
// 2019-03-13
class Orders extends \Magento\Framework\Model\AbstractModel {
	/** 2019-03-13 */
	function _construct() {$this->_init(R::class);}
}