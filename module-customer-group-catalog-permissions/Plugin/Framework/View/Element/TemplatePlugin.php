<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Framework\View\Element;

use Magento\Framework\View\Element\Template;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Framework\App\Http\Context;
use Magento\Customer\Model\Context as CustomerContext;

/**
 * Class TemplatePlugin
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Framework\View\Element
 */
class TemplatePlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param Config $config
     * @param Context $context
     */
    public function __construct(
        Config $config,
        Context $context
    ) {
        $this->config = $config;
        $this->context = $context;
    }

    /**
     * Modify cache keys
     *
     * @param Template $subject
     * @param array $result
     * @return array
     */
    public function afterGetCacheKeyInfo(Template $subject, $result)
    {
        if ($this->config->isEnabled()) {
            $result['customer_group'] = $this->context->getValue(CustomerContext::CONTEXT_GROUP);
        }

        return $result;
    }
}
