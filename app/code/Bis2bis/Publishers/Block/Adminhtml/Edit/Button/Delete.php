<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package     Bis2bis\Publishers\Block\Adminhtml\Edit\Button
 * @author      Beatriz Graciani Sborz
 *
 * Provides the "Delete" button configuration for the Publisher edit page.
 * The button appears only if the user has permission and an ID is present.
 */

namespace Bis2bis\Publishers\Block\Adminhtml\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\AuthorizationInterface;

/**
 * Class Delete
 *
 * Defines the logic for rendering the "Delete" button on the Publisher edit form.
 * The button is only shown if the user has permission and a Publisher ID is available.
 */
class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var AuthorizationInterface
     */
    protected AuthorizationInterface $authorization;

    /**
     * Delete constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->authorization = $context->getAuthorization();
        parent::__construct($context);
    }

    /**
     * Get button configuration for Delete action.
     *
     * @return array
     */
    public function getButtonData(): array
    {
        $id = $this->context->getRequest()->getParam('id');

        if (!$id || !$this->authorization->isAllowed('Bis2bis_Publishers::delete')) {
            return [];
        }

        return [
            'label'      => __('Delete'),
            'class'      => 'delete',
            'on_click'   => sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to delete this publisher?'),
                $this->getDeleteUrl()
            ),
            'sort_order' => 20,
        ];
    }

    /**
     * Get the delete URL for the current publisher.
     *
     * @return string
     */
    public function getDeleteUrl(): string
    {
        $id = $this->context->getRequest()->getParam('id');
        return $this->getUrl('*/*/delete', ['id' => $id]);
    }
}
