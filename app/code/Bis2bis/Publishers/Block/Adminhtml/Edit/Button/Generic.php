<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @package     Bis2bis\Publishers\Block\Adminhtml\Edit\Button
 * @author      Beatriz Graciani Sborz
 *
 */

namespace Bis2bis\Publishers\Block\Adminhtml\Edit\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Generic
 *
 * Provides common functionality for admin edit page buttons,
 * such as generating URLs using Magento's context.
 */
class Generic
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * Generic constructor.
     *
     * @param Context $context Context to retrieve URL builder and other utilities.
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Retrieve a URL based on the provided route and parameters.
     *
     * @param string $route  The route path (e.g., '* /* /index').
     * @param array  $params An associative array of parameters.
     * @return string The generated URL.
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
