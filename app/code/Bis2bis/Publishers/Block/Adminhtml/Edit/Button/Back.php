<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package     Bis2bis\Publishers\Block\Adminhtml\Edit\Button
 * @author      Beatriz Graciani Sborz
 */

namespace Bis2bis\Publishers\Block\Adminhtml\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class Back
 *
 * Provides configuration for the "Back" button in the admin publisher edit form.
 */
class Back extends Generic implements ButtonProviderInterface
{
    /**
     * Retrieve button configuration data.
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class'      => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * Get URL to redirect to when clicking the Back button.
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/');
    }
}
