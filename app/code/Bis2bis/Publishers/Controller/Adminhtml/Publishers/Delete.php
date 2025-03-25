<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package   Bis2bis\Publishers\Controller\Adminhtml\Publishers
 * @author    Beatriz Graciani Sborz
 *
 * Controller responsável por excluir uma editora do sistema.
 */

declare(strict_types=1);

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Bis2bis\Publishers\Model\PublisherFactory;

/**
 * Class Delete
 *
 * Realiza a exclusão de uma editora pelo ID.
 */
class Delete extends Action
{
    /**
     * @var PublisherFactory
     */
    protected PublisherFactory $publisherFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param PublisherFactory $publisherFactory
     */
    public function __construct(
        Context $context,
        PublisherFactory $publisherFactory
    ) {
        parent::__construct($context);
        $this->publisherFactory = $publisherFactory;
    }

    /**
     * Executa a exclusão da editora.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Publisher ID is not specified.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $publisher = $this->publisherFactory->create()->load($id);
            if (!$publisher->getId()) {
                $this->messageManager->addErrorMessage(__('This publisher no longer exists.'));
            } else {
                $publisher->delete();
                $this->messageManager->addSuccessMessage(__('You have deleted the publisher.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the publisher.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Verifica permissão para excluir uma editora.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::delete');
    }
}
