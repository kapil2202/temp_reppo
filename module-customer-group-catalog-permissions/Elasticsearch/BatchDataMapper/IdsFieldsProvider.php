<?php
namespace Aheadworks\CustGroupCatPermissions\Elasticsearch\BatchDataMapper;

/**
 * Class IdsFieldsProvider
 * @package Aheadworks\CustGroupCatPermissions\Elasticsearch\BatchDataMapper
 */
class IdsFieldsProvider
{
    /**
     * {@inheritdoc}
     */
    public function getFields(array $productIds, $storeId)
    {
        $fields = [];
        foreach ($productIds as $productId) {
            $fields[$productId] = ['entity_id' => $productId];
        }

        return $fields;
    }
}
