<?php

namespace Bis2bis\Publishers\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Patch to add the 'publisher' attribute to products.
 */
class AddPublisherAttribute implements DataPatchInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Constructor
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Apply the patch
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            Product::ENTITY,
            'publisher',
            [
                'type' => 'int',
                'label' => 'Publisher',
                'input' => 'select',
                'source' => \Bis2bis\Publishers\Model\Attribute\Source\Publisher::class,
                'required' => false,
                'sort_order' => 210,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'user_defined' => true,
                'group' => 'General',
                'used_in_product_listing' => true,
                'is_filterable_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_visible_in_graphql' => true,
                'is_filterable' => 1,
            ]
        );
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
