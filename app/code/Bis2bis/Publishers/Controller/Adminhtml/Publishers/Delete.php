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
use Magento\Framework\Exception\LocalizedException;
use Bis2bis\Publishers\Api\PublisherRepositoryInterface;

/**
 * Class Delete
 *
 * Realiza a exclusão de uma editora utilizando o PublisherRepositoryInterface.
 */
class Delete extends Action
{
    /**
     * @var PublisherRepositoryInterface
     */
    protected PublisherRepositoryInterface $publisherRepository;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PublisherRepositoryInterface $publisherRepository
     */
    public function __construct(
        Context $context,
        PublisherRepositoryInterface $publisherRepository
    ) {
        parent::__construct($context);
        $this->publisherRepository = $publisherRepository;
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
            $this->publisherRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('You have deleted the publisher.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while deleting the publisher.')
            );
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
