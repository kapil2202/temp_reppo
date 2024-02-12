<?php
namespace Aheadworks\CustGroupCatPermissions\Controller\Adminhtml;

use Aheadworks\CustGroupCatPermissions\Api\PermissionManagerInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Bulk
 * @package Aheadworks\CustGroupCatPermissions\Controller\Adminhtml
 */
abstract class Bulk extends Action
{
    /**#@+
     * Constants defined for keys of the request data array.
     */
    const ENTITY_TYPE_REQUEST_PARAM_KEY = 'entityType';
    const ENTITY_IDS_REQUEST_PARAM_KEY = 'entityIds';
    const IS_GRID_IDS_REQUEST_PARAM_KEY = 'isGrid';
    /**#@-*/

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var array
     */
    private $permissionManagers;

    /**
     * @var array
     */
    private $collectionFactories;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param array $permissionManagers
     * @param array $collectionFactories
     */
    public function __construct(
        Context $context,
        Filter $filter,
        array $permissionManagers = [],
        array $collectionFactories = []
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->permissionManagers = $permissionManagers;
        $this->collectionFactories = $collectionFactories;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data = $this->getRequest()->getParams();
        $result = [];

        if (!empty($data)) {
            try {
                $this->validateRequiredData($data);
                $result = $this->applyAction($data);
            } catch (\Exception $exception) {
                $result = [
                    'error' => $exception->getMessage()
                ];
            }
        }
        return $resultJson->setData($result);
    }

    /**
     * Validate required data
     *
     * @param array $data
     * @throws \Exception
     */
    private function validateRequiredData($data)
    {
        if ((empty($data[self::IS_GRID_IDS_REQUEST_PARAM_KEY]) && empty($data[self::ENTITY_IDS_REQUEST_PARAM_KEY]))
            || empty($data[self::ENTITY_TYPE_REQUEST_PARAM_KEY])) {
            throw new \Exception(
                (string)__('Some of required data is missing.')
            );
        }
    }

    /**
     * Retrieve entity type to apply permissions for
     *
     * @param array $data
     * @return string
     */
    protected function getEntityType(array $data)
    {
        return $data[self::ENTITY_TYPE_REQUEST_PARAM_KEY];
    }

    /**
     * Retrieve entity ids to apply permissions for
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    protected function getEntityIds(array $data)
    {
        if ($this->getRequest()->getParam(self::IS_GRID_IDS_REQUEST_PARAM_KEY, false)) {
            $entityType = $this->getEntityType($data);
            $collectionFactory = $this->getCollectionFactoryByType($entityType);
            $collection = $this->filter->getCollection($collectionFactory->create());
            $ids = $collection->getAllIds();
        } else {
            $ids = $data[self::ENTITY_IDS_REQUEST_PARAM_KEY];
        }

        return $ids;
    }

    /**
     * Retrieve permission manager by type
     *
     * @param string $type
     * @return bool|PermissionManagerInterface
     */
    protected function getPermissionManagerByType($type)
    {
        return isset($this->permissionManagers[$type])
            ? $this->permissionManagers[$type]
            : false;
    }

    /**
     * Retrieve collection factory by type
     *
     * @param string $type
     * @return bool|DataObject
     */
    private function getCollectionFactoryByType($type)
    {
        return isset($this->collectionFactories[$type])
            ? $this->collectionFactories[$type]
            : false;
    }

    /**
     * Apply action
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    abstract protected function applyAction($data);
}
