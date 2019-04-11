<?php
namespace Mangoit\MediaclipHub\Model\ResourceModel\Modules;
use Mangoit\MediaclipHub\Model\Modules as M;
use Mangoit\MediaclipHub\Model\ResourceModel\Modules as R;
// 2019-04-11
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
    /** 2019-04-11 */
    function _construct() {$this->_init(M::class, R::class);}
}
