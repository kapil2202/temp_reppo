<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Layer;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterList as LayerFilterList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;

/**
 * Class FilterListPlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Model\Layer
 */
class FilterListPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Applier
     */
    private $permissionApplier;

    /**
     * @param Config $config
     * @param Registry $registry
     * @param Applier $permissionApplier
     */
    public function __construct(
        Config $config,
        Registry $registry,
        Applier $permissionApplier
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->permissionApplier = $permissionApplier;
    }

    /**
     * Check is need to remove price from filters list
     *
     * @param LayerFilterList $subject
     * @param array $result
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFilters(LayerFilterList $subject, array $result)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        $currentCategory = $this->registry->registry('current_category');
        if ($currentCategory) {
            $this->permissionApplier->applyForCategory($currentCategory);
            if ($currentCategory->getData(Applier::HIDE_PRICE)) {
                $result = $this->removePriceFilter($result);
            }
        }

        return $result;
    }

    /**
     * Remove price filter from filters list
     *
     * @param array $filters
     * @return array
     */
    private function removePriceFilter(array $filters)
    {
        /** @var AbstractFilter $filter */
        foreach ($filters as $key => $filter) {
            try {
                if ($filter->getAttributeModel()->getAttributeCode() == ProductInterface::PRICE) {
                    unset($filters[$key]);
                    break;
                }
            } catch (LocalizedException $e) {
            }
        }

        return $filters;
    }
}
