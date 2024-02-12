<?php
namespace Aheadworks\CustGroupCatPermissions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CmsPagePermissionSearchResultsInterface
 * @package Aheadworks\CustGroupCatPermissions\Api\Data
 */
interface CmsPagePermissionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get permissions list
     *
     * @return \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface[]
     */
    public function getItems();

    /**
     * Set permissions list
     *
     * @param \Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
