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
use Bis2bis\Publishers\Model\PublisherFactory;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var PublisherFactory
     */
    protected PublisherFactory $publisherFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param PublisherFactory $publisherFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        PublisherFactory $publisherFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->publisherFactory = $publisherFactory;
    }

    /**
     * Executa a exibição do formulário de edição ou criação da editora.
     *
     * @return Page
     */
    public function execute(): Page
    {
        $id = (int) $this->getRequest()->getParam('id');
        $resultPage = $this->resultPageFactory->create();

        if ($id) {
            $publisher = $this->publisherFactory->create()->load($id);
            if (!$publisher->getId()) {
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
     * Verifica se o usuário atual tem permissão para editar uma editora.
     *
     * @return bool
     */
    protected function _isAllowed(): bool
    {
        return $this->_authorization->isAllowed('Bis2bis_Publishers::edit');
    }
}
