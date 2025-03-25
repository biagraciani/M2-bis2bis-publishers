<?php
/**
 * PublisherList Resolver for GraphQL.
 *
 * @package Bis2bis\Publishers\GraphQl\Resolver
 * @author Beatriz Graciani Sborz
 */

declare(strict_types=1);

namespace Bis2bis\Publishers\GraphQl\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory;

/**
 * Class PublisherList
 *
 * Resolves the list of publishers for GraphQL queries.
 */
class PublisherList implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * PublisherList constructor.
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Resolve the "publishers" GraphQL query.
     *
     * If the optional "entity_id" argument is passed, filters the collection by it.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): array {
        $collection = $this->collectionFactory->create();

        if (!empty($args['entity_id'])) {
            $collection->addFieldToFilter('entity_id', (int) $args['entity_id']);
        }

        $items = [];
        foreach ($collection as $publisher) {
            $items[] = [
                'entity_id' => (int) $publisher->getEntityId(),
                'name'      => $publisher->getName(),
                'address'   => $publisher->getAddress(),
                'logo'      => $publisher->getLogo(),
                'status'    => (bool) $publisher->getStatus(),
                'cnpj'      => $publisher->getCnpj(),
            ];
        }

        return $items;
    }
}
