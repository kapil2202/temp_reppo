<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Plugin\Catalog\Block\Product;

use Magento\Catalog\Block\Product\View as ProductView;
use Aheadworks\CustGroupCatPermissions\Model\Service\DomManager;
use Aheadworks\CustGroupCatPermissions\Model\ProductPermission\Applier;

/**
 * Class ViewPlugin
 */
class ViewPlugin
{
    /**
     * ViewPlugin constructor.
     *
     * @param DomManager $domManager
     */
    public function __construct(
        private DomManager $domManager
    ) {
    }

    /**
     * Modify product view content HTML
     *
     * @param ProductView $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml(ProductView $subject, string $resultHtml): string
    {
        if ($subject->getProduct()->getData(Applier::HIDE_PRICE) &&
            $subject->getNameInLayout() === 'opengraph.general') {
            $dom = $this->domManager->createDomFromHtml($resultHtml);
            $xPath = $this->domManager->prepareXpath($dom);

            $nodes = $xPath->query("//meta[@property='product:price:amount']");
            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }

            $resultHtml =  $this->domManager->convertDomToHtml($dom);
        }

        return $resultHtml;
    }
}
