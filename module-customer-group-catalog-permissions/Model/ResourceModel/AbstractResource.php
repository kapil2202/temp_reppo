<?php
namespace Aheadworks\CustGroupCatPermissions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class AbstractResource
 * @package Aheadworks\CustGroupCatPermissions\Model\ResourceModel
 */
abstract class AbstractResource extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Save object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $object->validateBeforeSave();
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Delete object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param int $objectId
     * @param string $field
     * @return $this
     */
    public function load(AbstractModel $object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $arguments = $this->getArgumentsForLoading();
            $this->entityManager->load($object, $objectId, $arguments);
        }
        return $this;
    }

    /**
     * Retrieve arguments array for entity loading
     *
     * @return array
     */
    protected function getArgumentsForLoading()
    {
        return [];
    }
}
