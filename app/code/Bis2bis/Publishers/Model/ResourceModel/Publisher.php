<?php
declare(strict_types=1);

namespace Bis2bis\Publishers\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource Model for Publisher entity
 */
class Publisher extends AbstractDb
{
    /**
     * Initialize main table and primary key field.
     */
    protected function _construct(): void
    {
        $this->_init('bis2bis_publishers', 'entity_id');
    }
}
