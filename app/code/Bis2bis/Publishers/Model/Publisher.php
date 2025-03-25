<?php
declare(strict_types=1);

/**
 * Publisher Model
 *
 * @category Bis2bis
 * @package  Bis2bis_Publishers
 * @author   Beatriz Graciani Sborz
 * @license  Open Software License (OSL 3.0)
 */

namespace Bis2bis\Publishers\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Publisher
 *
 * Model class for publisher entity.
 */
class Publisher extends AbstractModel
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(\Bis2bis\Publishers\Model\ResourceModel\Publisher::class);
    }
}
