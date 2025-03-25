<?php

namespace Bis2bis\Publishers\Model\ResourceModel\Publisher;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bis2bis\Publishers\Model\Publisher', 'Bis2bis\Publishers\Model\ResourceModel\Publisher');
    }
}
