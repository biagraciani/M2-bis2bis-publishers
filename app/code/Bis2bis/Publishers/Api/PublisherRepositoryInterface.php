<?php
namespace Bis2bis\Publishers\Api;

use Bis2bis\Publishers\Api\Data\PublisherInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface PublisherRepositoryInterface
{
    /**
     * Salva os dados da Editora.
     *
     * @param PublisherInterface $publisher
     * @return PublisherInterface
     */
    public function save(PublisherInterface $publisher);

    /**
     * Retorna uma Editora por ID.
     *
     * @param int $publisherId
     * @return PublisherInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($publisherId);

    /**
     * Deleta uma Editora.
     *
     * @param PublisherInterface $publisher
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(PublisherInterface $publisher);

    /**
     * Deleta uma Editora pelo ID.
     *
     * @param int $publisherId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($publisherId);

    /**
     * Retorna uma lista de Editoras com base nos critérios de busca.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
