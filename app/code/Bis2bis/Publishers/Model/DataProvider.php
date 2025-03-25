<?php
declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package Bis2bis\Publishers\Model
 * @author Beatriz Graciani Sborz
 */

namespace Bis2bis\Publishers\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Bis2bis\Publishers\Model\ResourceModel\Publisher\CollectionFactory;

/**
 * Class DataProvider
 *
 * Provides data for the Publisher form in the Admin UI.
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array|null
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    protected $collection;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $publisherCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $publisherCollectionFactory,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $publisherCollectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Retrieve and format the form data.
     *
     * @return array
     */
    public function getData(): array
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }

        $this->loadedData = [];

        foreach ($this->collection->getItems() as $item) {
            $data = $item->getData();

            if ($item->getLogo()) {
                $data['logo'] = [[
                    'name' => $item->getLogo(),
                    'url' => $this->getMediaUrl() . $item->getLogo(),
                    'type' => 'image'
                ]];
            }

            $this->loadedData[$item->getId()] = $data;
        }

        return $this->loadedData;
    }

    /**
     * Get media base URL.
     *
     * @return string
     */
    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }
}
