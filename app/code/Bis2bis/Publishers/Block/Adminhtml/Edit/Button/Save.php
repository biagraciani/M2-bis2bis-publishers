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

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 *
 * Provides configuration data for the "Save" button on the publisher edit page,
 * including additional split options such as "Save & New" and "Save & Close".
 */
class Save extends Generic implements ButtonProviderInterface
{
    /**
     * Retrieve the configuration data for the Save button.
     *
     * @return array
     */
    public function getButtonData(): array
    {
        return [
            'label'          => __('Save'),
            'class'          => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [[
                            'targetName' => 'publishers_form.publishers_form',
                            'actionName' => 'save',
                            'params'     => [false],
                        ]],
                    ],
                ],
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options'    => $this->getOptions(),
        ];
    }

    /**
     * Retrieve additional options for the Save button.
     *
     * These options are rendered as part of a split button
     * and allow for alternative save actions.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'id_hard' => 'save_and_new',
                'label'   => __('Save & New'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [[
                                'targetName' => 'publishers_form.publishers_form',
                                'actionName' => 'save',
                                'params'     => [true, ['back' => 'add']],
                            ]],
                        ],
                    ],
                ],
            ],
            [
                'id_hard' => 'save_and_close',
                'label'   => __('Save & Close'),
                'data_attribute' => [
                    'mage-init' => [
                        'buttonAdapter' => [
                            'actions' => [[
                                'targetName' => 'publishers_form.publishers_form',
                                'actionName' => 'save',
                                'params'     => [true],
                            ]],
                        ],
                    ],
                ],
            ],
        ];
    }
}
