<?php
declare(strict_types=1);

/**
 * MassDelete controller.
 *
 * Handles mass deletion of publisher records using the PublisherRepositoryInterface.
 *
 * If the "excluded" parameter equals 'false', all grid items are selected;
 * otherwise, deletion is performed on items specified in the "selected" parameter.
 *
 * @package Bis2bis\Publishers\Controller\Adminhtml\Publishers
 *
 * @author Beatriz Graciani Sborz
 *
 */

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Bis2bis\Publishers\Api\PublisherRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;


class MassDelete extends Action
{
    /**
     * Filter to retrieve selected grid items.
     *
     * @var Filter
     */
    protected Filter $filter;

    /**
     * Request interface instance.
     *
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * Collection factory for publisher entities.
     *
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * Publisher repository for CRUD operations.
     *
     * @var PublisherRepositoryInterface
     */
    protected PublisherRepositoryInterface $publisherRepository;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Filter $filter
     * @param RequestInterface $request
     * @param CollectionFactory $collectionFactory
     * @param PublisherRepositoryInterface $publisherRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filter $filter,
        RequestInterface $request,
        CollectionFactory $collectionFactory,
        PublisherRepositoryInterface $publisherRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * Execute the mass delete action.
     *
     * Determines the collection to delete based on the "selected" and "excluded" parameters.
     *
     * @return Redirect
     * @throws LocalizedException
     */
    public function execute(): Redirect
    {
        $selected = $this->request->getParam('selected');
        $excluded = $this->request->getParam('excluded');

        // If "excluded" equals 'false', it means all items in the grid are selected.
        if ($excluded === 'false') {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
        } else {
            if (!is_array($selected) || empty($selected)) {
                throw new LocalizedException(__('An item needs to be selected. Select and try again.'));
            }
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['in' => $selected]);
        }

        $deletedCount = 0;
        foreach ($collection as $item) {
            try {
                $this->publisherRepository->delete($item);
                $deletedCount++;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while deleting one of the publishers.')
                );
            }
        }

        if ($deletedCount) {
            $this->messageManager->addSuccessMessage(__('A total of %1 element(s) have been deleted.', $deletedCount));
        } else {
            $this->messageManager->addErrorMessage(__('No publishers were deleted.'));
        }

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
