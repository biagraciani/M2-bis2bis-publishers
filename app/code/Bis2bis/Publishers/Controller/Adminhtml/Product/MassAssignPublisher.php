<?php

namespace Bis2bis\Publishers\Controller\Adminhtml\Product;

use Magento\Catalog\Controller\Adminhtml\Product;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class MassAssignPublisher extends Product implements HttpPostActionInterface
{
    public function execute()
    {
        $publisherId = $this->getRequest()->getParam('publisher_id');
        $productIds = $this->getRequest()->getParam('product_ids');

        if (empty($publisherId) || empty($productIds)) {
            $this->messageManager->addErrorMessage(__('Please select a publisher and products.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $publisher = $this->_objectManager->create('Bis2bis\Publishers\Model\Publisher')->load($publisherId);
            if (!$publisher->getId()) {
                throw new LocalizedException(__('Publisher not found.'));
            }

            $products = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
                ->addFieldToFilter('entity_id', ['in' => $productIds]);

            foreach ($products as $product) {
                // Associar a editora ao produto (exemplo: usando um atributo customizado)
                $product->setData('publisher_id', $publisherId);
                $product->save();
            }

            $this->messageManager->addSuccessMessage(__('The selected products have been assigned to the publisher.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: ') . $e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
