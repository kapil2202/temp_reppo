<?php
declare(strict_types=1);

namespace Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Page\Menu;

use Aheadworks\CustGroupCatPermissions\Block\Adminhtml\Page\Menu;
use Magento\Backend\Block\Template;

/**
 * Page Menu Item
 *
 * @method string getPath()
 * @method string getLabel()
 * @method string getResource()
 * @method string getController()
 * @method string getAction()
 * @method array getLinkAttributes()
 * @method Item setLinkAttributes(array $linkAttributes)
 */
class Item extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_CustGroupCatPermissions::page/menu/item.phtml';

    /**
     * Prepare html attributes of the link
     *
     * @return void
     */
    protected function prepareLinkAttributes(): void
    {
        $linkAttributes = is_array($this->getLinkAttributes()) ? $this->getLinkAttributes() : [];
        if (!isset($linkAttributes['href'])) {
            $linkAttributes['href'] = $this->getUrl($this->getPath());
        }
        $classes = [];
        if (isset($linkAttributes['class'])) {
            $classes = explode(' ', $linkAttributes['class']);
        }
        if ($this->isCurrent()) {
            $classes[] = 'current';
        }
        $linkAttributes['class'] = implode(' ', $classes);
        $this->setLinkAttributes($linkAttributes);
    }

    /**
     * Retrieves string presentation of link attributes
     *
     * @return string
     */
    public function serializeLinkAttributes(): string
    {
        $nameValuePairs = [];
        foreach ($this->getLinkAttributes() as $attrName => $attrValue) {
            $nameValuePairs[] = sprintf('%s="%s"', $attrName, $attrValue);
        }
        return implode(' ', $nameValuePairs);
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout(): self
    {
        $this->prepareLinkAttributes();
        if ($this->isCurrent()) {
            /** @var Menu $menu */
            $menu = $this->getParentBlock();
            $menu?->setTitle($this->getLabel());
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        if ($this->getResource() && !$this->_authorization->isAllowed($this->getResource())) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Checks whether the item is current
     *
     * @return bool
     */
    private function isCurrent(): bool
    {
        if ($this->getAction()) {
            $result = $this->getController() === $this->getRequest()->getControllerName() &&
                $this->getAction() === $this->getRequest()->getActionName();
        } else {
            $result = $this->getController() == $this->getRequest()->getControllerName();
        }

        return $result;
    }
}
