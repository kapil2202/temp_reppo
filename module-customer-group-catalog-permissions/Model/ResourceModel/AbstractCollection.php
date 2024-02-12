<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as FrameworkAbstractCollection;
use Magento\Framework\DB\Select;

/**
 * Class AbstractCollection
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel
 */
abstract class AbstractCollection extends FrameworkAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Retrieve collection items grouped
     *
     * @param   string $columnName
     * @return  array
     */
    public function getItemsGroupedByColumn($columnName)
    {
        $groupedItems = [];

        foreach ($this->getItems() as $item) {
            $columnValue = $item->getData($columnName);
            if (!empty($columnValue)) {
                if (isset($groupedItems[$columnValue])) {
                    $groupedItems[$columnValue][] = $item->getData();
                } else {
                    $groupedItems[$columnValue] = [$item->getData()];
                }
            }
        }

        return $groupedItems;
    }

    /**
     * Attach entity table data to collection items
     *
     * @param string|Select $table
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @param array $conditions
     * @param array $order
     * @param bool $setDataAsArray
     * @param array $default
     * @return $this
     */
    protected function attachRelationTable(
        $table,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName,
        $conditions = [],
        $order = [],
        $setDataAsArray = false,
        $default = []
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $table instanceof Select
                ? $table
                : $connection->select()->from(['tmp_table' => $this->getTable($table)]);

            $select->where('tmp_table.' . $linkageColumnName . ' IN (?)', $ids);

            foreach ($conditions as $condition) {
                $select->where(
                    'tmp_table.' . $condition['field'] . ' ' . $condition['condition'] . ' (?)',
                    $condition['value']
                );
            }

            if (!empty($order)) {
                $select->order('tmp_table.' . $order['field'] . ' ' . $order['direction']);
            }
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = [];
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        if (is_array($columnNameRelationTable)) {
                            $fieldValue = [];
                            foreach ($columnNameRelationTable as $columnNameRelation) {
                                $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                            }
                            $result[] = $fieldValue;
                        } else {
                            $result[] = $data[$columnNameRelationTable];
                        }
                    }
                }
                if (!empty($result)) {
                    $fieldData = $setDataAsArray ? $result : array_shift($result);
                    $item->setData($fieldName, $fieldData);
                } else if (!empty($default) && $default[$linkageColumnName] == $id) {
                    $item->setData($fieldName, $default[$columnNameRelationTable]);
                }
            }
        }
        return $this;
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string|Select $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @param array $conditions
     * @return $this
     */
    protected function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName,
        $conditions = []
    ) {
        $linkageTableName = is_string($tableName) ? $tableName : $columnFilter . '_at';

        if (!in_array($linkageTableName, $this->linkageTableNames)) {
            $this->linkageTableNames[] = $linkageTableName;
            $table = $tableName instanceof Select
                ? new \Zend_Db_Expr('(' . $tableName . ')')
                : $this->getTable($tableName);

            $this->getSelect()->joinLeft(
                [$linkageTableName => $table],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );
            foreach ($conditions as $condition) {
                $this->getSelect()->where(
                    $linkageTableName . '.' . $condition['field'] . ' ' . $condition['condition'] . ' (?)',
                    $condition['value']
                );
            }
        }

        $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
        return $this;
    }
}
