<?php
namespace Aheadworks\CustGroupCatPermissions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CategoryPermissionSearchResultsInterface
 * @package Aheadworks\CustGroupCatPermissions\Api\Data
 */
interface CategoryPermissionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get permissions list
     *
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface[]
     */
    public function getItems();

    /**
     * Set permissions list
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CategoryPermissionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
