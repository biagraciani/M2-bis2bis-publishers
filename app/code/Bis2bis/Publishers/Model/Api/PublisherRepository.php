<?php
declare(strict_types=1);

namespace Bis2bis\Publishers\Model\Api;

use Bis2bis\Publishers\Api\PublisherRepositoryInterface;
use Bis2bis\Publishers\Api\Data\PublisherInterface;
use Bis2bis\Publishers\Api\Data\PublisherInterfaceFactory;
use Bis2bis\Publishers\Model\ResourceModel\Publisher as PublisherResource;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory as PublisherCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * PublisherRepository class
 *
 * This class implements the PublisherRepositoryInterface to provide CRUD operations
 * and list retrieval for Publisher entities. It interacts with the resource model and
 * collection factory to persist and query data, following Magento's service contracts.
 */
class PublisherRepository implements PublisherRepositoryInterface
{
    /**
     * @var PublisherResource
     */
    protected $publisherResource;

    /**
     * @var PublisherInterfaceFactory
     */
    protected $publisherFactory;

    /**
     * @var PublisherCollectionFactory
     */
    protected $publisherCollectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * Constructor.
     *
     * @param PublisherResource $publisherResource
     * @param PublisherInterfaceFactory $publisherFactory
     * @param PublisherCollectionFactory $publisherCollectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        PublisherResource $publisherResource,
        PublisherInterfaceFactory $publisherFactory,
        PublisherCollectionFactory $publisherCollectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->publisherResource = $publisherResource;
        $this->publisherFactory = $publisherFactory;
        $this->publisherCollectionFactory = $publisherCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save a publisher.
     *
     * @param PublisherInterface $publisher
     * @return PublisherInterface
     * @throws LocalizedException
     */
    public function save(PublisherInterface $publisher)
    {
        try {
            $this->publisherResource->save($publisher);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Unable to save publisher: %1', $e->getMessage()));
        }
        return $publisher;
    }

    /**
     * Get a publisher by ID.
     *
     * @param int $publisherId
     * @return PublisherInterface
     * @throws NoSuchEntityException
     */
    public function getById($publisherId)
    {
        $publisher = $this->publisherFactory->create();
        $this->publisherResource->load($publisher, $publisherId);
        if (!$publisher->getId()) {
            throw new NoSuchEntityException(__('Publisher with id "%1" does not exist.', $publisherId));
        }
        return $publisher;
    }

    /**
     * Delete a publisher.
     *
     * @param PublisherInterface $publisher
     * @return bool
     * @throws LocalizedException
     */
    public function delete(PublisherInterface $publisher)
    {
        try {
            $this->publisherResource->delete($publisher);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Unable to delete publisher: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * Delete a publisher by ID.
     *
     * @param int $publisherId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function deleteById($publisherId)
    {
        $publisher = $this->getById($publisherId);
        return $this->delete($publisher);
    }

    /**
     * Retrieve a list of publishers matching the search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->publisherCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
