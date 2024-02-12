<?php
namespace Aheadworks\CustGroupCatPermissions\Plugin\Framework\App\PageCache;

use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Resolver\CurrentCategory as CurrentCategoryResolver;
use Magento\Framework\App\PageCache\Identifier;
use Aheadworks\CustGroupCatPermissions\Model\Config;
use Magento\Catalog\Model\Session as CatalogSession;

/**
 * Class IdentifierPlugin
 *
 * @package Aheadworks\CustGroupCatPermissions\Plugin\Framework\App\PageCache
 */
class IdentifierPlugin
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CurrentCategoryResolver
     */
    private $resolver;

    /**
     * @var CatalogSession
     */
    private $catalogSession;

    /**
     * @param Config $config
     * @param CurrentCategoryResolver $resolver
     * @param CatalogSession $catalogSession
     */
    public function __construct(
        Config $config,
        CurrentCategoryResolver $resolver,
        CatalogSession $catalogSession
    ) {
        $this->config = $config;
        $this->resolver = $resolver;
        $this->catalogSession = $catalogSession;
    }

    /**
     * Add to the current unique page identifier a category id from the previous page, if category can be detected
     *
     * @param Identifier $subject
     * @param string $value
     * @return string
     */
    public function afterGetValue($subject, $value)
    {
        if (!$this->config->isEnabled()) {
            return $value;
        }

        $finalValue = $value;
        $categoryId = $this->resolver->getCurrentCategoryIdFpc();

        if (!empty($categoryId)) {
            $finalValue = $finalValue . $categoryId;
            $this->catalogSession->setLastVisitedCategoryId($categoryId);
        }

        return $finalValue;
    }
}
