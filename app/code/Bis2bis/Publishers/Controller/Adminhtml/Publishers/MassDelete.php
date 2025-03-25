<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package   Bis2bis\Publishers\Controller\Adminhtml\Publishers
 * @author    Beatriz Graciani Sborz
 *
 */

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;

/**
 * Class MassDelete
 *
 * Handles the mass deletion of publisher records in the admin panel.
 */
class MassDelete extends Action
{
    /**
     * Filter used to retrieve selected items in the grid.
     *
     * @var Filter
     */
    protected Filter $filter;

    /**
     * Collection factory for publisher entities.
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute the mass delete action.
     *
     * Deletes all selected publisher records and displays a success message.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $item) {
            $item->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 element(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if the current admin user is allowed to delete publishers.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::delete');
    }
}
