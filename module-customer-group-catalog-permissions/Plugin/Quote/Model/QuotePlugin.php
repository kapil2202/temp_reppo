<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Quote\Model;

use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;
use Magento\Quote\Model\Quote;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\Registry;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

/**
 * Class QuotePlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Quote\Model
 */
class QuotePlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Config $config
     * @param Registry $registry
     */
    public function __construct(
        Config $config,
        Registry $registry
    ) {
        $this->config = $config;
        $this->registry = $registry;
    }

    /**
     * Set skip permission check flag
     *
     * @param Quote $subject
     * @param \Closure $proceed
     * @return AbstractCollection
     */
    public function aroundGetAllVisibleItems(Quote $subject, \Closure $proceed)
    {
        if (!$this->config->isEnabled()) {
            return $proceed();
        }

        $this->registry->register(Applier::SKIP_PERMISSION_APPLY, true, true);
        $result = $proceed();
        $this->registry->unregister(Applier::SKIP_PERMISSION_APPLY);

        return $result;
    }
}
