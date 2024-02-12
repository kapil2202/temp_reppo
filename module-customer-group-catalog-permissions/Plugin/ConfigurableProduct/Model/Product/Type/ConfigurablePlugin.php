<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Model\Product\Type;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Collection\SalableProcessor;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Class ConfigurablePlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\ConfigurableProduct\Model\Product\Type
 */
class ConfigurablePlugin
{
    /**
     * @var SalableProcessor
     */
    private $salableProcessor;

    /**
     * @param SalableProcessor $salableProcessor
     */
    public function __construct(
        SalableProcessor $salableProcessor
    ) {
        $this->salableProcessor = $salableProcessor;
    }

    /**
     * Additional check collection of configurable childes to prevent issue with salable parent,
     * when all childes are not salable
     *
     * @param ConfigurableProductType $configurableProductType
     * @param bool $isSalable
     * @param Product $product
     * @return bool
     */
    public function afterIsSalable($configurableProductType, $isSalable, $product)
    {
        if ($isSalable !== false) {
            $childCollection = $this->getChildProductCollection($configurableProductType, $product);
            $isSalable = $this->checkIfParentCanBeSalable($childCollection);
        }
        return $isSalable;
    }

    /**
     * @param ConfigurableProductType $configurableProductType
     * @param Product $product
     * @return Collection
     */
    protected function getChildProductCollection($configurableProductType, $product)
    {
        $childCollection = $configurableProductType->getUsedProductCollection($product);
        $childCollection->addStoreFilter($configurableProductType->getStoreFilter($product));
        $childCollection = $this->salableProcessor->process($childCollection);
        return $childCollection;
    }

    /**
     * @param Collection $childCollection
     * @return bool
     */
    protected function checkIfParentCanBeSalable($childCollection)
    {
        $isParentSalable = false;
        /** @var Product $childProduct */
        foreach ($childCollection->getItems() as $childProduct) {
            if ($childProduct->isSalable()) {
                $isParentSalable = true;
                break;
            }
        }
        return $isParentSalable;
    }
}
