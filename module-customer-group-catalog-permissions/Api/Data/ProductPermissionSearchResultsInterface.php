<?php
namespace Aheadworks\CustGroupCatPermissions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProductPermissionSearchResultsInterface
 * @package Aheadworks\CustGroupCatPermissions\Api\Data
 */
interface ProductPermissionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get permissions list
     *
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface[]
     */
    public function getItems();

    /**
     * Set permissions list
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\ProductPermissionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
