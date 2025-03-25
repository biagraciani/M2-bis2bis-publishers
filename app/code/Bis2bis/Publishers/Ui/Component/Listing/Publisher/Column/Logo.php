<?php
/**
 * Logo Column Renderer for Publisher Grid
 *
 * @category  Bis2bis
 * @package   Bis2bis_Publishers
 * @author    Beatriz Graciani Sborz
 */

namespace Bis2bis\Publishers\Ui\Component\Listing\Publisher\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Logo
 *
 * Displays publisher logo thumbnails in the admin grid.
 */
class Logo extends Column
{
    /**
     * Field used for image alt text.
     */
    public const ALT_FIELD = 'name';

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items']) && is_array($dataSource['data']['items'])) {
            $columnName = $this->getData('name');
            $mediaBaseUrl = $this->storeManager
                ->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $defaultLogoUrl = $mediaBaseUrl . 'catalog/product/placeholder/image.jpg';

            foreach ($dataSource['data']['items'] as &$item) {
                $relativeLogoPath = !empty($item['logo']) ? ltrim($item['logo'], '/') : '';
                $fullLogoUrl = $relativeLogoPath
                    ? $mediaBaseUrl . $relativeLogoPath
                    : $defaultLogoUrl;

                $item["{$columnName}_src"] = $fullLogoUrl;
                $item["{$columnName}_alt"] = $this->getAlt($item) ?: __('No Logo');
                $item["{$columnName}_orig_src"] = $fullLogoUrl;
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt Text
     *
     * @param array $row
     * @return string|null
     */
    protected function getAlt(array $row): ?string
    {
        return $row[self::ALT_FIELD] ?? null;
    }
}
