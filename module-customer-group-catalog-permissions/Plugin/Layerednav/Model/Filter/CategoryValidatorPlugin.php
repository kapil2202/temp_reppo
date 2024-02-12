<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Layerednav\Model\Filter;

use Aheadworks\CustGroupCatPermissions\Model\Config;
use Aheadworks\CustGroupCatPermissions\Model\CategoryPermission\Applier;
use Magento\Framework\Interception\InterceptorInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Class CategoryValidatorPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Layerednav\Model\Filter
 */
class CategoryValidatorPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Applier
     */
    private $permissionApplier;

    /**
     * @param Config $config
     * @param Applier $permissionApplier
     */
    public function __construct(
        Config $config,
        Applier $permissionApplier
    ) {
        $this->config = $config;
        $this->permissionApplier = $permissionApplier;
    }

    /**
     * Check if need to remove price from filters list
     *
     * @param InterceptorInterface $subject
     * @param bool $result
     * @param AbstractExtensibleModel $filter
     * @param CategoryInterface|CategoryModel $category
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate($subject, $result, $filter, $category)
    {
        if (!$this->config->isEnabled()) {
            return $result;
        }

        if (is_object($category)) {
            $this->permissionApplier->applyForCategory($category);
            if ($category->getData(Applier::HIDE_PRICE)) {
                if ($this->isFilterRelatedToPrice($filter)) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * Check if current filter related to the price attribute
     *
     * @param AbstractExtensibleModel $filter
     * @return bool
     */
    protected function isFilterRelatedToPrice($filter)
    {
        $result = false;
        try {
            $result = ($filter->getCode() == ProductInterface::PRICE);
        } catch (\Exception $e) {
        }

        return $result;
    }
}
