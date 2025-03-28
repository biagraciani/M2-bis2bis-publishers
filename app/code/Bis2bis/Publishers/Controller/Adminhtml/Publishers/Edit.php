<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package   Bis2bis_Publishers
 * @author    Beatriz Graciani Sborz
 *
 * Controller responsável por exibir a tela de edição de uma editora no admin.
 */

declare(strict_types=1);

namespace Bis2bis\Publishers\Controller\Adminhtml\Publishers;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Bis2bis\Publishers\Api\PublisherRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Edit Publisher Controller.
 *
 * Exibe o formulário para edição ou criação de uma editora.
 */
class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var PublisherRepositoryInterface
     */
    protected PublisherRepositoryInterface $publisherRepository;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PublisherRepositoryInterface $publisherRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PublisherRepositoryInterface $publisherRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory   = $resultPageFactory;
        $this->publisherRepository = $publisherRepository;
    }

    /**
     * Execute the edit action.
     *
     * @return Page
     */
    public function execute(): Page
    {
        $id = (int) $this->getRequest()->getParam('id');
        $resultPage = $this->resultPageFactory->create();

        if ($id) {
            try {
                $this->publisherRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This publisher no longer exists.'));
                return $resultPage;
            }
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Publisher'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Publisher'));
        }

        return $resultPage;
    }

    /**
     * Check if current user is allowed to edit a publisher.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::edit');
    }
}
