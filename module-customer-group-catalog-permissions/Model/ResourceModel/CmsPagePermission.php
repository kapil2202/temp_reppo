<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel;

use Aheadworks\CustGroupCatPermissions\Api\Data\CmsPagePermissionInterface;

/**
 * Class CmsPagePermission
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel
 */
class CmsPagePermission extends AbstractResource
{
    /**#@+
     * Constants defined for table
     * used by corresponding entity
     */
    const MAIN_TABLE_NAME = 'aw_cp_cms_page_permissions';
    const MAIN_TABLE_ID_FIELD_NAME = 'permission_id';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, self::MAIN_TABLE_ID_FIELD_NAME);
    }
    
    /**
     * Get permission id by CMS page id
     *
     * @param int $pageId
     * @param int $groupId
     * @param int $storeId
     * @return string
     */
    public function getPermissionIdByCmsPageId($pageId, $groupId, $storeId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(self::MAIN_TABLE_NAME), CmsPagePermissionInterface::ID)
            ->where(CmsPagePermissionInterface::CMS_PAGE_ID . ' = ' . $pageId)
            ->where(CmsPagePermissionInterface::CUSTOMER_GROUP_ID . ' = ' . $groupId . ' '
                . ' OR ' . CmsPagePermissionInterface::CUSTOMER_GROUP_ID . ' IS NULL')
            ->where(CmsPagePermissionInterface::STORE_ID . ' = ' . $storeId . ' '
                . ' OR ' . CmsPagePermissionInterface::STORE_ID . ' IS NULL');

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Delete permissions for CMS pages by ids
     *
     * @param array $pageIds
     */
    public function deleteForPages($pageIds)
    {
        $pageIds = implode(',', $pageIds);
        $this->getConnection()->delete(
            $this->getTable(self::MAIN_TABLE_NAME),
            [CmsPagePermissionInterface::CMS_PAGE_ID . ' IN (' . $pageIds . ')']
        );
    }
}
