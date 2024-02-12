<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Elasticsearch\Model\Adapter\FieldMapper\Product;

use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProviderInterface;

/**
 * Class FieldProviderPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Elasticsearch\Model\Adapter\FieldMapper\Product
 */
class FieldProviderPlugin
{
    /**
     * Push entity_id to index config
     *
     * @param FieldProviderInterface $subject
     * @param array $result
     * @return array
     */
    public function afterGetFields(
        $subject,
        $result
    ) {
        $result['entity_id'] = ['type' => 'integer'];

        return $result;
    }
}
