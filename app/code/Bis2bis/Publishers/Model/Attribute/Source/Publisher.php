<?php
namespace Bis2bis\Publishers\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory;

class Publisher extends AbstractSource
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Retorna todas as opções para o atributo (somente editoras ativas)
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', 1);
            $options = [];
            foreach ($collection as $publisher) {
                $options[] = [
                    'label' => $publisher->getName(),
                    'value' => $publisher->getId()
                ];
            }
            $this->_options = $options;
        }
        return $this->_options;
    }
}
